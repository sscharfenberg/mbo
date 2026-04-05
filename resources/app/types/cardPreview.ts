/** A single legality entry for a card format. */
export interface CardLegality {
    format: string;
    legality: string;
}

/** Response shape from the card stack preview endpoint. */
export interface CardPreview {
    name: string;
    card_image_0: string | null;
    card_image_1: string | null;
    set_code: string | null;
    set_name: string | null;
    set_path: string | null;
    collector_number: string;
    artist: string | null;
    amount: number;
    condition: string | null;
    finish: string | null;
    language: string;
    created_at: string;
    updated_at: string;
    price: number;
    total_price: number;
    scryfall_uri: string | null;
    legalities: CardLegality[];
}