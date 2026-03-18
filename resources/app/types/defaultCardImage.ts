/** A single card-image focused defaultCard (front and optional back face). */
export type DefaultCardImage = {
    id: string;
    name: string;
    card_image_0: string | null;
    card_image_1: string | null;
    artist: string | null;
    set: { name: string; code: string };
};