import type { Type as FlashType } from "Components/Visual/FlashMessage.vue";

export {};

declare module "@inertiajs/core" {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: {
                user: {
                    id: number;
                    name: string;
                    email: string;
                } | null;
            };
            locale: string;
            supportedLocales: string[];
            features: {
                registration: boolean;
                resetPasswords: boolean;
                emailVerification: boolean;
            };
            flash: {
                message: string;
                type: FlashType;
            };
        };
    }
}
