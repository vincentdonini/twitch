/**
 * Mapping: muscle slug → { front: path IDs in muscles-front.svg, back: path IDs in muscles-back.svg }
 *
 * HOW TO FILL THIS IN:
 * 1. Open packages/ui/src/assets/muscle-inspector.html in your browser
 * 2. Select a muscle on the right panel
 * 3. Click the corresponding paths in the front or back SVG (they turn orange)
 * 4. Click "Assign selected paths to muscle"
 * 5. When done → "Export mapping" → copy result here
 *
 * Notes:
 * - Front paths: pathN (e.g. "path85")  — from muscles-front.svg
 * - Back paths:  pathN (e.g. "path2")   — from muscles-back.svg
 * - IDs are unique across both SVGs so no prefix is needed
 */
export type MusclePaths = { front: string[]; back: string[] }

export const MUSCLE_SLUG_TO_PATHS: Record<string, MusclePaths> = {
  // Core
  // ---------------------------------------------------------------------------
  "rectus-abdominis": {
    front: [
      "path85",
      "path87",
      "path123",
      "path125",
      "path117",
      "path119",
      "path21",
      "path23",
    ],
    back: [],
  },
  "obliques": {
    front: [
      "path207",
      "path209",
      "path247",
      "path249",
      "path251",
      "path253",
      "path223",
      "path225",
      "path81",
      "path83",
    ],
    back: [],
  },
  "transverse-abdominis": {
    front: [],
    back: [],
  },

  // Arms
  // ---------------------------------------------------------------------------
  "forearms": {
    front: [
      "path45",
      "path47",
      "path227",
      "path229",
      "path137",
      "path139",
    ],
    back: [
      "bpath97",
      "bpath99",
      "bpath181",
      "bpath183",
      "bpath193",
      "bpath195",
    ],
  },
  "triceps": {
    front: [
      "path109",
      "path111",
    ],
    back: [
      "bpath149",
      "bpath151",
      "bpath185",
      "bpath187",
      "bpath105",
      "bpath107",
    ],
  },
  "biceps": {
    front: [
      "path89",
      "path91",
    ],
    back: [],
  },

  // Shoulders
  // ---------------------------------------------------------------------------
  "anterior-deltoid": {
    front: ["path37", "path39"],
    back: [],
  },
  "lateral-deltoid": {
    front: [],
    back: [],
  },
  "posterior-deltoid": {
    front: [],
    back: [
      "bpath65",
      "bpath67",
    ],
  },

  // Back
  // ---------------------------------------------------------------------------
  "latissimus-dorsi": {
    front: [],
    back: [
      "bpath13",
      "bpath15",
    ],
  },
  "trapezius": {
    front: [],
    back: [
      "bpath17",
      "bpath19",
    ],
  },
  "erector-spinae": {
    front: [],
    back: [
      "bpath29",
      "bpath31",
    ],
  },

  // Chest
  // ---------------------------------------------------------------------------
  "pectoralis-major": {
    front: ["path5", "path7"],
    back: [],
  },

  // Glutes
  // ---------------------------------------------------------------------------
  "glutes": {
    front: [],
    back: [
      "bpath1",
      "bpath3",
    ],
  },

  // Legs
  // ---------------------------------------------------------------------------
  "quadriceps": {
    front: [
      "path9",
      "path11",
      "path141",
      "path143",
      "path169",
      "path171",
    ],
    back: [],
  },
  "hamstrings": {
    front: [],
    back: [
      "bpath25",
      "bpath27",
      "bpath53",
      "bpath55",
      "",
    ],
  },
  "calves": {
    front: [],
    // front: [
    //   "path49",
    //   "path51",
    //   "path75",
    //   "path73",
    //   "path129",
    //   "path131",
    //   "path199",
    //   "path201",
    // ],
    back: [
      "bpath33",
      "bpath35",
      "bpath41",
      "bpath43",
    ],
  },
  "adductors": {
    front: [
      "path77",
      "path79",
      "path93",
      "path95",
      "path215",
      "path217",
    ],
    back: [],
  },
}
