<script lang="ts" setup>
import { Link } from "@inertiajs/vue3";
const startYear = 2026;
const currentYear = new Date().getFullYear();
let copyrightDate = `${startYear}`;
if (currentYear > startYear) {
    copyrightDate += " - " + currentYear;
}
</script>

<template>
    <footer>
        &copy; Sven Scharfenberg {{ copyrightDate }}
        <nav aria-label="Footer Navigation">
            <Link class="text-link" href="/privacy">{{ $t("footer.privacy") }}</Link>
            <Link class="text-link" href="/imprint">{{ $t("footer.imprint") }}</Link>
            <a href="https://github.com/sscharfenberg/mbo"><img src="./github.svg" alt="Github Repository" /></a>
        </nav>
    </footer>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/mixins" as m;
@use "Abstracts/sizes" as s;

footer {
    display: flex;
    flex-direction: column;

    gap: 2ch;

    @include m.mq("portrait") {
        align-items: center;
        flex-direction: row;

        a {
            margin-left: auto;
        }
    }

    @include m.mqset(
        "padding",
        map.get(s.$footer, "padding", "base"),
        map.get(s.$footer, "padding", "portrait"),
        map.get(s.$footer, "padding", "landscape"),
        map.get(s.$footer, "padding", "desktop")
    );

    > nav {
        display: flex;
        flex-direction: column;

        gap: 1ch;

        @include m.mq("portrait") {
            align-items: center;
            flex-direction: row;

            margin-left: auto;
            gap: 1.5ch;
        }
    }

    a {
        display: flex;
        align-items: center;

        img {
            width: 24px;
            height: auto;
        }
    }
}
</style>
