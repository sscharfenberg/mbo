import type { DefaultCardArtCrop } from "Types/defaultCardArtCrop";

/** Serialized container as returned by ContainerController (list, show, edit). */
export interface Container {
    id: string;
    name: string;
    description: string | null;
    /** ContainerType enum value (e.g. "binder", "deckbox", "other"). */
    type: string;
    /** ContainerVisibility enum value ("private" or "public"). */
    visibility: string;
    /** Free-text label used when type === "other". */
    custom_type: string | null;
    /** Persisted sort_order from the database. */
    sort: number;
    /** Default card art crop data, or null if no default card is set. */
    defaultCard: DefaultCardArtCrop | null;
    /** Sum of all card stack amounts in this container. */
    totalCards: number;
    /** Total price of all card stacks in the user's selected currency. */
    totalPrice: number;
}