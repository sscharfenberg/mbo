<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import Stats from "@/pages/Guest/Welcome/Stats.vue";
import Headline from "Components/UI/Headline.vue";
import Icon from "Components/UI/Icon.vue";

defineProps<{
    oracleCards: { num: number; size: number };
    defaultCards: { num: number; size: number };
    sets: number;
}>();
</script>

<template>
    <Head
        ><title>{{ $t("pages.welcome.title") }}</title></Head
    >
    <headline>
        <icon name="home" :size="3" />
        {{ $t("pages.welcome.title") }}
    </headline>
    <stats :oracle-cards="oracleCards" :default-cards="defaultCards" :sets="sets" />
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. At cumque dignissimos eius nihil perferendis possimus
        praesentium qui soluta ullam voluptatibus. Cumque ea libero non possimus soluta. Accusantium ad adipisci aliquid
        at atque commodi, consectetur corporis deserunt, dolorem dolores eos expedita id illo ipsum laborum maxime
        mollitia nam nobis nostrum perferendis quia quod ratione recusandae reiciendis rem sapiente tempora, temporibus
        vero voluptate voluptatum. A ab adipisci aliquid amet architecto asperiores assumenda beatae dolor dolores eius
        et excepturi hic ipsa itaque labore laboriosam laudantium minima minus nam nulla, numquam obcaecati officiis
        quas qui quos reiciendis repellat repellendus reprehenderit sequi suscipit tempora voluptate?
    </p>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. At cumque dignissimos eius nihil perferendis possimus
        praesentium qui soluta ullam voluptatibus. Cumque ea libero non possimus soluta. Accusantium ad adipisci aliquid
        at atque commodi, consectetur corporis deserunt, dolorem dolores eos expedita id illo ipsum laborum maxime
        mollitia nam nobis nostrum perferendis quia quod ratione recusandae reiciendis rem sapiente tempora, temporibus
        vero voluptate voluptatum. A ab adipisci aliquid amet architecto asperiores assumenda beatae dolor dolores eius
        et excepturi hic ipsa itaque labore laboriosam laudantium minima minus nam nulla, numquam obcaecati officiis
        quas qui quos reiciendis repellat repellendus reprehenderit sequi suscipit tempora voluptate?
    </p>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. At cumque dignissimos eius nihil perferendis possimus
        praesentium qui soluta ullam voluptatibus. Cumque ea libero non possimus soluta. Accusantium ad adipisci aliquid
        at atque commodi, consectetur corporis deserunt, dolorem dolores eos expedita id illo ipsum laborum maxime
        mollitia nam nobis nostrum perferendis quia quod ratione recusandae reiciendis rem sapiente tempora, temporibus
        vero voluptate voluptatum. A ab adipisci aliquid amet architecto asperiores assumenda beatae dolor dolores eius
        et excepturi hic ipsa itaque labore laboriosam laudantium minima minus nam nulla, numquam obcaecati officiis
        quas qui quos reiciendis repellat repellendus reprehenderit sequi suscipit tempora voluptate?
    </p>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. At cumque dignissimos eius nihil perferendis possimus
        praesentium qui soluta ullam voluptatibus. Cumque ea libero non possimus soluta. Accusantium ad adipisci aliquid
        at atque commodi, consectetur corporis deserunt, dolorem dolores eos expedita id illo ipsum laborum maxime
        mollitia nam nobis nostrum perferendis quia quod ratione recusandae reiciendis rem sapiente tempora, temporibus
        vero voluptate voluptatum. A ab adipisci aliquid amet architecto asperiores assumenda beatae dolor dolores eius
        et excepturi hic ipsa itaque labore laboriosam laudantium minima minus nam nulla, numquam obcaecati officiis
        quas qui quos reiciendis repellat repellendus reprehenderit sequi suscipit tempora voluptate?
    </p>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. At cumque dignissimos eius nihil perferendis possimus
        praesentium qui soluta ullam voluptatibus. Cumque ea libero non possimus soluta. Accusantium ad adipisci aliquid
        at atque commodi, consectetur corporis deserunt, dolorem dolores eos expedita id illo ipsum laborum maxime
        mollitia nam nobis nostrum perferendis quia quod ratione recusandae reiciendis rem sapiente tempora, temporibus
        vero voluptate voluptatum. A ab adipisci aliquid amet architecto asperiores assumenda beatae dolor dolores eius
        et excepturi hic ipsa itaque labore laboriosam laudantium minima minus nam nulla, numquam obcaecati officiis
        quas qui quos reiciendis repellat repellendus reprehenderit sequi suscipit tempora voluptate?
    </p>
</template>
