import { jaroWinkler } from "./jaroWinklerMatchAlogirthm";

export const escapeTag = (content) => {
    return content.replace(/(<([^>]+)>)/gi, "");
};

export const findBestMatch = (input, list, key = "title") => {
    let bestMatch = null;
    let bestScore = -1;

    for (const candidate of list) {
        const score = jaroWinkler(input.toLowerCase(), candidate[key].toLowerCase());
        if (score > bestScore) {
            bestScore = score;
            bestMatch = candidate;
        }
    }

    return { match: bestMatch, score: bestScore };

}
