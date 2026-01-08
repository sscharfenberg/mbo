/******************************************************************************
 * check node version
 * @type {module}
 *****************************************************************************/
import { createRequire } from "node:module";
import semver from "semver";
import chalk from "chalk";

const require = createRequire(import.meta.url); // https://nodejs.org/dist/latest-v18.x/docs/api/module.html#modulecreaterequirefilename
const { engines } = require("../../package.json");
const currentVersion = semver.clean(process.version);
const versionRequirement = engines.node;

/**
 * check node version and exit with error code 1 (uncaught exception) if
 * version requirements are not met
 */
(() => {
    if (!semver.satisfies(currentVersion, versionRequirement)) {
        console.log(
            [
                chalk.bgRed.white(" ERR "),
                chalk.cyan("current node version is"),
                chalk.yellowBright(currentVersion) + chalk.cyan(","),
                chalk.cyan("project requires"),
                chalk.redBright(versionRequirement) + chalk.cyan(".")
            ].join(" ")
        );
        process.exit(1);
    } else {
        console.log(
            [
                chalk.bgGreen.white(" OK "),
                chalk.cyan("current node version is"),
                chalk.greenBright(currentVersion) + chalk.cyan(".")
            ].join(" ")
        );
    }
})();
