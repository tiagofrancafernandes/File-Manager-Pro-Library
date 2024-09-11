export function formatSize(bytes) {
    bytes = !isNaN(bytes - 0) ? (bytes - 0) : 0;
    bytes = bytes >= 0 ? bytes : 0;
    const kilobytes = bytes / 1000; // Converte bytes para kilobytes

    if (kilobytes < 1024) {
        return `${(kilobytes).toFixed(1)} KB`; // Formata para uma casa decimal
    }

    if (kilobytes >= 1024) {
        return `${(kilobytes / 1000).toFixed(1)} MB`; // Formata para uma casa decimal
    }

    return `${(kilobytes).toFixed(1)} KB`; // Formata para uma casa decimal
}
