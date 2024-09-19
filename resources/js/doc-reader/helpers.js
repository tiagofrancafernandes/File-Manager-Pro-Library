export function debugLog(content, mode = 'log') {
    if (!window?.DEBUG) {
        return;
    }

    mode = ['throw', 'log', 'error'].includes(mode) ? mode : 'log';

    if (mode === 'log') {
        console.log(content);
        return;
    }

    if (mode === 'throw') {
        throw `${content}`;
        return;
    }

    if (mode === 'error') {
        console.error(new Error(`${content}`));
        return;
    }

    console.error(`Invalid mode`);
}
