/** A single card result as returned by the /api/card-image/search endpoint. */
export type CardResult = {
    id: string;
    name: string;
    art_crop: string;
    set: { name: string; code: string };
};