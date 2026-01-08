/******************************************************************************
 * merge svg icon files into a single svg file with symbols
 * CLI options
 * -v verbose output
 * -w watch files for changes
 * -i inline SVG, defaults to false
 *****************************************************************************/
import chalk from "chalk";
import processAgs from "minimist";
import fs from "node:fs";
import path from "node:path";
import { argv, cwd, exit, hrtime } from "node:process";
import { optimize } from "svgo";
import svgStore from "svgstore";
const args = processAgs(argv.slice(2));
const PROJECTROOT = fs.realpathSync(cwd());
const ICONS_DIR = path.resolve(PROJECTROOT, "resources/app/assets/icons");
const INLINE = args.i || false; // true = no DOCTYPE and xml/xmlns attribute
const OUT_DIR = path.resolve(PROJECTROOT, "storage/app/public");
const OUT_PATH = path.resolve(OUT_DIR, "sprite.svg");

/**
 * the global prefix for all icon log messages
 * @type {string}
 */
const logPrefix = !args.w && chalk.bgCyan.black("[" + chalk.italic("icons") + "]");

/**
 * info prefix for all icon log messages
 * @type {string}
 */
const infoPrefix = chalk.bgWhiteBright.black(" 🧐 nfo ");

/**
 * debug prefix for all icon log messages
 * @type {string}
 */
const debugPrefix = chalk.bgYellow.black(" 😏 dbg ");

/**
 * error prefix for all icon log messages
 * @type {string}
 */
const errorPrefix = chalk.bgRed.white(" 🫣 err ");

/**
 * log object with [info|debug|error] methods
 * @type {Object}
 */
const log = {
    info: text => {
        let msg = "";
        if (!args.w) msg += logPrefix;
        console.log(`${msg}${infoPrefix} ${chalk.whiteBright(text)}`);
    },
    debug: text => {
        let msg = "";
        if (!args.w) msg += logPrefix;
        console.log(`${msg}${debugPrefix} ${chalk.yellowBright(text)}`);
    },
    error: text => {
        let msg = "";
        if (!args.w) msg += logPrefix;
        console.log(`${msg}${errorPrefix} ${chalk.redBright(text)}`);
    }
};

/**
 * @function read directory and get filenames of svg files
 * @returns {string[]} array with filename strings
 */
const getSvgFileNames = () => {
    const fileNames = fs.readdirSync(ICONS_DIR).filter(file => !file.isDirectory && file.endsWith(".svg"));
    if (fileNames.length > 0) return fileNames;
    return [];
};

/**
 * @function write sprite to disk
 * @param {string} data - SVG contents as string
 * @param {number} length - the amount of SVGs processed.
 * @param {BigInt} start - the time the processing of the sprite sheet was started in BigInt nanoseconds
 */
const writeSpriteToDisk = (data, length, start) => {
    if (!fs.existsSync(OUT_DIR)) {
        args.v && log.info(`creating output directory ${chalk.yellow(path.basename(OUT_DIR))}`);
        fs.mkdirSync(OUT_DIR);
    }
    log.info(`writing icon sprite with ${chalk.bgRed.white(" " + length + " ")} symbols`);
    fs.writeFile(OUT_PATH, data, err => {
        if (err) {
            log.error(err);
            exit(1);
        } else {
            log.info(chalk.bgGreenBright.black(" 🥳 success! "));
            const end = hrtime.bigint();
            log.debug(`processing took ${chalk.bgYellow.black(" " + formatHrTime(end - start) + " ")} seconds.`);
        }
    });
};

/**
 * @function format HrTime in a human-readable format
 * @param {BigInt} time
 * @returns {string}
 */
const formatHrTime = time => {
    return (Number(time) / 1000000000).toFixed(3);
};

/**
 * @function event handler for firing a watch event
 * @param event
 * @param fileName
 */
const onIconChanged = (event, fileName) => {
    log.info(`file ${chalk.bgYellow.black(" " + fileName + " ")} triggered watcher`);
    createIconSprite();
};

/**
 * @function watch icon files for changes
 * once triggered, the watcher is killed and one second later resurrected
 * there are two reasons for this:
 * 1) depending on the OS, the watch function is triggered twice - once with "change", once with "rename" ¯\_(ツ)_/¯
 * 2) if more than one file is copied into the watched directory, the watcher gets triggered once per file.
 */
const watch = () => {
    const watcher = fs.watch(ICONS_DIR, { recursive: true }, (event, filename) => {
        watcher.close(); // first, kill the watcher so the event only triggers once.
        onIconChanged(event, filename); // event handler for the watch event
        setTimeout(watch, 1000); // resurrect the watcher after 1s
    });
};

/**
 * @function main function that creates the icon sprite
 */
export const createIconSprite = () => {
    const svgFiles = getSvgFileNames();
    let counter = 0;
    // prepare svgStore instance
    const sprites = svgStore({
        cleanDefs: true,
        cleanSymbols: true,
        svgAttrs: {
            class: "icon-sprite",
            "aria-hidden": "true",
            xmlns: "http://www.w3.org/2000/svg"
        }
    });
    const time = hrtime.bigint();
    log.info("preparing svg icon sprite sheet.");
    args.v && log.info(`found ${chalk.bgRed.white(" " + svgFiles.length + " ")} svg icon files`);
    // only proceed if we do have icons.
    if (svgFiles.length > 0) {
        svgFiles.forEach(svgFile => {
            // loop all icons
            const id = svgFile.replace(/\.[^/.]+$/, "");
            const filePath = path.join(ICONS_DIR, svgFile);
            const fileContents = fs.readFileSync(filePath, { encoding: "utf8" });
            // optimize svg file with svgo
            const optimizedSvg = optimize(fileContents, {
                multipass: true
            }).data;
            optimizedSvg && args.v && log.debug(id); // verbose output of icon id
            counter++;
            sprites.add(id, optimizedSvg, { symbolAttrs: { "aria-role": "icon" } }); // add to sprite
            if (counter === svgFiles.length) {
                // if all icons are processed
                writeSpriteToDisk(
                    sprites.toString({
                        inline: INLINE
                    }),
                    svgFiles.length,
                    time
                );
            }
        });
    } else {
        exit(0);
    }
};

/**
 * main script execution via node resources/build/icons.js
 */
args.v && log.debug("verbose output selected");
args.w && log.debug("watch option selected");
args.i && log.debug("inline option selected");
if (args.w) {
    log.info(`watching icon directory for changes ʕ•ᴥ•ʔ`);
    watch();
} else {
    createIconSprite();
}
