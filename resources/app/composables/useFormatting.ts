import { intervalToDuration } from "date-fns";

/**
 * @function format a decimal number into human-readable format
 * @param {number} num
 * @return {string}
 */
export function formatDecimals(num: number): string {
    return num.toLocaleString("de-DE", { style: "decimal" });
}

/**
 * @function format a duration in seconds to human readable "6d 2h 55m 14s" format
 * @param {number} seconds
 * @return string
 */
export function formatSeconds(seconds: number): string {
    const d = intervalToDuration({ start: 0, end: seconds * 1000 });
    const dateArr = [];
    console.log(d);
    if (d.years && d.years > 0) dateArr.push(`${d.months}M`);
    if (d.months && d.months > 0) dateArr.push(`${d.months}M`);
    if (d.days && d.days > 0) dateArr.push(`${d.days}d`);
    if (d.hours && d.hours > 0) dateArr.push(`${d.hours}h`);
    if (d.minutes && d.minutes > 0) dateArr.push(`${d.minutes}m`);
    if (d.seconds && d.seconds > 0) dateArr.push(`${d.seconds}s`);
    return dateArr.join(" ");
}
