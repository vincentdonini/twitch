interface CommandInfo {
    description: string;
}

export const commandRegistry: Record<string, CommandInfo> = {
    '!music': { description: 'Affiche la musique en cours de lecture.' },
    '!commands': { description: 'Liste toutes les commandes disponibles.' },
    '!help': { description: 'Affiche l\'aide pour une commande spécifique. Usage : !help [commande]' },
};
