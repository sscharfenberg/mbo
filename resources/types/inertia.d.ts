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
        };
    }
}
