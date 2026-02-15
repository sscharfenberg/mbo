<script lang="ts" setup>
import { Link } from "@inertiajs/vue3";
import LinkGroup from "Components/Visual/LinkGroup.vue";
const startYear = 2026;
const currentYear = new Date().getFullYear();
let copyrightDate = `${startYear}`;
if (currentYear > startYear) {
    copyrightDate += " - " + currentYear;
}
</script>

<template>
    <footer>
        <section class="inner">
            &copy; Sven Scharfenberg {{ copyrightDate }}
            <link-group :label="$t('footer.nav-label')">
                <Link class="text-link" href="/privacy">{{ $t("footer.privacy") }}</Link>
                <Link class="text-link" href="/imprint">{{ $t("footer.imprint") }}</Link>
                <a href="https://github.com/sscharfenberg/mbo"><img src="./github.svg" alt="Github Repository" /></a>
            </link-group>
        </section>
    </footer>
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/colors" as c;
@use "Abstracts/mixins" as m;
@use "Abstracts/sizes" as s;

footer {
    position: relative;

    margin-top: auto;

    background-color: map.get(c.$footer, "background");
    backdrop-filter: blur(12px);
    color: map.get(c.$footer, "surface");

    &::before {
        position: absolute;
        inset: 0;
        z-index: -1;

        border-top: map.get(s.$footer, "border") solid transparent;

        background: linear-gradient(to right, map.get(c.$footer, "border-from"), map.get(c.$footer, "border-to"))
            border-box;

        mask:
            linear-gradient(black, black) border-box,
            linear-gradient(black, black) padding-box;
        mask-composite: subtract;

        content: "";
    }

    @include m.mqset(
        "padding",
        map.get(s.$footer, "padding", "base"),
        map.get(s.$footer, "padding", "portrait"),
        map.get(s.$footer, "padding", "landscape"),
        map.get(s.$footer, "padding", "desktop")
    );

    .inner {
        display: flex;
        flex-direction: column;

        max-width: map.get(s.$app, "cage");
        margin: 0 auto;
        gap: 2ch;

        @include m.mq("portrait") {
            align-items: center;
            flex-direction: row;

            nav {
                margin-left: auto;
            }
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
