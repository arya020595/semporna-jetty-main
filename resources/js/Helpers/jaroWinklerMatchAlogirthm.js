const jaroWinkler = (s1, s2) => {
    const m = getMatchingCharacters(s1, s2);
    const t = getTranspositions(s1, s2, m);

    const len1 = s1.length;
    const len2 = s2.length;
    const matchCount = m.length;

    if (matchCount === 0) return 0;

    const jaro = (
        (matchCount / len1) +
        (matchCount / len2) +
        ((matchCount - t) / matchCount)
    ) / 3;

    const prefix = getCommonPrefixLength(s1, s2);
    const scalingFactor = 0.1; // Winkler boost factor

    return jaro + prefix * scalingFactor * (1 - jaro);
}

// Helper: matching characters within allowable distance
const getMatchingCharacters = (s1, s2) => {
    const matchDistance = Math.floor(Math.max(s1.length, s2.length) / 2) - 1;
    const matched1 = Array(s1.length).fill(false);
    const matched2 = Array(s2.length).fill(false);
    const matches = [];

    for (let i = 0; i < s1.length; i++) {
        const start = Math.max(0, i - matchDistance);
        const end = Math.min(i + matchDistance + 1, s2.length);

        for (let j = start; j < end; j++) {
            if (!matched2[j] && s1[i] === s2[j]) {
                matched1[i] = true;
                matched2[j] = true;
                matches.push(s1[i]);
                break;
            }
        }
    }

    return matches;
}

// Helper: count transpositions
const getTranspositions = (s1, s2, matches) => {
    const matched1 = [];
    const matched2 = [];

    let matchDistance = Math.floor(Math.max(s1.length, s2.length) / 2) - 1;
    const matched2Flags = Array(s2.length).fill(false);

    // Collect matched characters from s1
    for (let i = 0; i < s1.length; i++) {
        const start = Math.max(0, i - matchDistance);
        const end = Math.min(i + matchDistance + 1, s2.length);
        for (let j = start; j < end; j++) {
            if (!matched2Flags[j] && s1[i] === s2[j]) {
                matched1.push(s1[i]);
                matched2.push(s2[j]);
                matched2Flags[j] = true;
                break;
            }
        }
    }

    let transpositions = 0;
    for (let i = 0; i < matched1.length; i++) {
        if (matched1[i] !== matched2[i]) transpositions++;
    }

    return transpositions / 2;
}

// Helper: common prefix length (up to 4)
const getCommonPrefixLength = (s1, s2) => {
    const maxPrefixLength = 4;
    let i = 0;
    while (i < maxPrefixLength && s1[i] === s2[i]) i++;
    return i;
}

export {
    jaroWinkler
};
