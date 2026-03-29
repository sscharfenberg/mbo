/** Response shape from the card stack preview endpoint. */
export interface CardPreview {
    name: string;
    card_image_0: string | null;
    card_image_1: string | null;
    set_code: string | null;
    set_name: string | null;
    set_icon: string | null;
    collector_number: string;
    artist: string | null;
    amount: number;
    condition: string | null;
    finish: string | null;
    price: number;
    total_price: number;
    scryfall_uri: string | null;
}