interface ImportMetaEnv {
    // ... could include environment variables

    /** Stripe API Key */
    readonly VITE_STRIPE_API_KEY: string;
}

interface ImportMeta {
    readonly env: ImportMetaEnv;
    glob<T = any>(pattern: string): Record<string, () => Promise<T>>;
}
