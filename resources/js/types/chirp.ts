export type Chirp = {
    id: number;
    title: string;
    description: string | null;
    image_url: string | null;
    created_at: string | null;
    user: {
        id: number;
        name: string;
    };
};
