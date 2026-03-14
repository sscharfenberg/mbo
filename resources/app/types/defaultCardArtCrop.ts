/** A single artCrop focussed defaultCard */
export type DefaultCardArtCrop = {
    id: string;
    name: string;
    art_crop: string;
    artist: string | null;
    set: { name: string; code: string };
};
