/******************************************************************************
 * check node version
 *****************************************************************************/
import { createRequire } from "node:module";
import chalk from "chalk";
import semver from "semver";

const require = createRequire(import.meta.url);
const { engines } = require("../../package.json") as { engines: { node: string } };
const currentVersion: string | null = semver.clean(process.version);
const versionRequirement: string = engines.node;

/**
 * check node version and exit with error code 1 (uncaught exception) if
 * version requirements are not met
 */
(() => {
    if (!semver.satisfies(currentVersion as string, versionRequirement)) {
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