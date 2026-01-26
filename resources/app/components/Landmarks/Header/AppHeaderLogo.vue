<template>
    <img src="./logo.svg" alt="MtG Binder Organizer Logo" />
</template>

<style scoped lang="scss">
@use "sass:map";
@use "Abstracts/mixins" as m;
@use "Abstracts/sizes" as s;

img {
    width: 4ch;
    height: auto;

    @include m.mq("portrait") {
        width: 6ch;
    }

    @include m.mq("landscape") {
        width: 8ch;
    }
}
</style>
