export const debugIsOn = () => {
    return Boolean(window?.DEBUG || globalThis?.APP_DEBUG);
}

export function debugLog(content, mode = 'log') {
    if (!debugIsOn()) {
        return;
    }

    mode = ['throw', 'log', 'error', 'debug'].includes(mode) ? mode : 'log';

    if (mode === 'log') {
        console.log('[DEBUG]', content);
        return;
    }

    if (mode === 'debug') {
        console.debug('[DEBUG]', content);
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
