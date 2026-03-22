/** Row shape for card stacks in the DataTable, as returned by ContainerController::show. */
export interface CardStackRow {
    id: string;
    name: string;
    set_name: string;
    set_code: string;
    collector_number: string;
    amount: number;
    condition: string | null;
    foil_type: string | null;
    language: string;
    art_crop: string | null;
}