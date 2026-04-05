import type { CardStackRow } from "Types/cardStackRow";

/** Row shape for the collection-wide card stacks DataTable. */
export interface CollectionCardStackRow extends CardStackRow {
    /** Name of the container this stack belongs to, or null if unassigned. */
    container_name: string | null;
    /** UUID of the container, or null if unassigned. */
    container_id: string | null;
}
