<script lang="ts" setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <footer>
        &copy; Sven Scharfenberg 2025
        <nav aria-label="Footer Navigation">
            <Link class="text-link" href="/privacy">Datenschutz</Link>
            <Link class="text-link" href="/imprint">Impressum</Link>
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

    gap: 1ch;

    @include m.mq("portrait") {
        align-items: center;
        flex-direction: row;

        a {
            margin-left: auto;
        }
    }

    > nav {
        display: flex;
        flex-direction: column;

        gap: 1ch;

        @include m.mq("portrait") {
            align-items: center;
            flex-direction: row;

            margin-left: auto;
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
