import {NextResponse} from 'next/server';
import type {NextRequest} from 'next/server';

// Cache en mémoire pour éviter de fetcher plusieurs fois le même slug
// Clé = "type-id", valeur = slug
const slugCache = new Map<string, string>();

// Génère un slug SEO-friendly à partir du nom ou titre et de l'id
export const generateSlug = (name: string, id: number) => {
    const slug = name
        .normalize("NFD")                  // décompose les accents
        .replace(/[\u0300-\u036f]/g, "")   // supprime les diacritiques
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "-")       // tout sauf lettres/chiffres => "-"
        .replace(/^-+|-+$/g, "")           // supprime les tirets en début/fin
        .replace(/--+/g, "-");             // supprime les tirets multiples

    return `${slug}-${id}`;
};

export async function middleware(req: NextRequest) {
    const url = req.nextUrl.clone();

    // Définir les routes dynamiques et l'API correspondante
    const routes: {
        pattern: RegExp;
        apiPath: (id: string) => string;
        type: string;
    }[] = [
        {
            pattern: /^\/wods\/(\d+)$/,
            apiPath: (id) => `${process.env.API_URL}/wods/${id}`,
            type: 'wod',
        },
        {
            pattern: /^\/categories\/(\d+)$/,
            apiPath: (id) => `${process.env.API_URL}/categories/${id}`,
            type: 'category',
        },
        {
            pattern: /^\/movements\/(\d+)$/,
            apiPath: (id) => `${process.env.API_URL}/movements/${id}`,
            type: 'movement',
        },
        {
            pattern: /^\/muscles\/(\d+)$/,
            apiPath: (id) => `${process.env.API_URL}/muscles/${id}`,
            type: 'muscle',
        },
        {
            pattern: /^\/muscle-groups\/(\d+)$/,
            apiPath: (id) => `${process.env.API_URL}/muscle-groups/${id}`,
            type: 'muscle-group',
        },
        {
            pattern: /^\/muscle-areas\/(\d+)$/,
            apiPath: (id) => `${process.env.API_URL}/muscle-areas/${id}`,
            type: 'muscle-area',
        },
    ];

    for (const route of routes) {
        const match = url.pathname.match(route.pattern);
        if (match) {
            const id = match[1];
            const cacheKey = `${route.type}-${id}`;
            let slug = slugCache.get(cacheKey);

            // Si le slug n'est pas en cache, fetch depuis l'API
            if (!slug) {
                try {
                    const res = await fetch(route.apiPath(id));
                    if (!res.ok) break; // Laisser passer si l'ID n'existe pas
                    const data = await res.json();

                    // Générer le slug SEO
                    slug = generateSlug(data.title || data.name, Number(id));
                    slugCache.set(cacheKey, slug); // Stocker dans le cache
                } catch (err) {
                    console.error(`Impossible de récupérer ${route.type} ${id}`, err);
                    break;
                }
            }

            // Redirection ou rewrite selon le type de route
            if (route.type === 'wod') {
                // Redirection 308 pour SEO : Google voit l'URL canonique
                url.pathname = `/wods/${slug}`;
                return NextResponse.redirect(url, 308);
            } else {
                // Rewrite côté serveur : l'URL reste inchangée mais sert la page avec slug
                url.pathname = `/${route.type}s/${slug}`;
                return NextResponse.rewrite(url);
            }
        }
    }

    // Continuer normalement pour les autres routes
    return NextResponse.next();
}

// Routes sur lesquelles le middleware doit s'appliquer
export const config = {
    matcher: ["/wods/:path*", "/categories/:path*", "/movements/:path*"],
};
