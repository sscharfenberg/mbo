declare module "svgstore" {
    interface SvgStoreOptions {
        cleanDefs?: boolean;
        cleanSymbols?: boolean;
        svgAttrs?: Record<string, string>;
    }

    interface SvgStore {
        add(id: string, svg: string, options?: { symbolAttrs?: Record<string, string> }): SvgStore;
        toString(options?: { inline?: boolean }): string;
    }

    export default function svgStore(options?: SvgStoreOptions): SvgStore;
}