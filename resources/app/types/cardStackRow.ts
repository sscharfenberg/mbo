/** Row shape for card stacks in the DataTable, as returned by ContainerController::show. */
export interface CardStackRow {
    id: string;
    name: string;
    set_name: string;
    set_code: string;
    set_icon: string | null;
    collector_number: string;
    amount: number;
    condition: string | null;
    finish: string | null;
    language: string;
    art_crop: string | null;
    /** Unit price of one card in the user's selected currency. */
    price: number;
    /** Total price of the stack (unit price × amount). */
    total_price: number;
}