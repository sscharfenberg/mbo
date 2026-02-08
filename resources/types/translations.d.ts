import type { ComponentCustomProperties } from "vue";

declare module "vue" {
    interface ComponentCustomProperties {
        $t: (key: string) => string;
    }
}
