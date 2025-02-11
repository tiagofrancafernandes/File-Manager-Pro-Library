import { debugIsOn, debugLog } from '@/doc-reader/helpers';

globalThis.fullScreenToggle = function () {
    if (!document.fullscreenElement) {
        // Se não estiver em tela cheia, ativa
        document.documentElement.requestFullscreen().catch(err => {
            console.error(`Erro ao tentar ativar tela cheia: ${err.message}`);
        });

        return;
    }

    // Se já estiver em tela cheia, sai
    document.exitFullscreen().catch(err => {
        console.error(`Erro ao tentar sair da tela cheia: ${err.message}`);
    });
};

const zoomActorsInit = () => {
    document
        .querySelectorAll('button[data-id="zoom"][data-value]')
        ?.forEach(button => {
            button.addEventListener('click', event => {
                let data = event?.target?.dataset || {};
                let actionCode = data?.value || null;

                if (!actionCode || typeof actionCode !== 'string') {
                    return;
                }

                document.querySelector('body')?.dispatchEvent(
                    new CustomEvent('keydown', {
                        detail: { code: actionCode }
                    })
                );
            });
        });
};

const fullScreenActorsInit = () => {
    let canvasEl = document.querySelector('body canvas');

    canvasEl?.addEventListener('dblclick', event => {
        if (!globalThis?.fullScreenToggle) {
            return;
        }

        globalThis?.fullScreenToggle();
    });

    let fullScreenButton = document.querySelector('button[data-id="full"]');
    if (!fullScreenButton) {
        return;
    }

    fullScreenButton.addEventListener('click', event => {
        if (!globalThis?.fullScreenToggle) {
            return;
        }

        globalThis?.fullScreenToggle();
    });
};

const init = elSelector => {
    // container = document.querySelector('body canvas');
    // container = document.querySelector('body div');
    // container.scrollTop = 100;

    fullScreenActorsInit();
    zoomActorsInit();

    const CONFIG = {
        defaultZoom: 50,
        keepZoomPreference: true,
    }

    const setContainerWidth = (value = CONFIG?.defaultZoom, keepPreference = true) => {
        if (typeof value === 'string' && value.endsWith('%')) {
            value = value.slice(0, value.indexOf('%')).trim();
        }

        value = new RegExp('^((100)|([1-9])0)$').test(`${value}`)
            ? Number(value)
            : CONFIG?.defaultZoom;

        value = isNaN(value) ? CONFIG?.defaultZoom : value;

        if (!keepPreference || !CONFIG?.keepZoomPreference) {
            localStorage.removeItem('zoom_level');
        }

        if (keepPreference || CONFIG?.keepZoomPreference) {
            localStorage.setItem('zoom_level', value);
        }

        container.style.width = `${value}%`;
    };

    let container = document.querySelector(elSelector);

    if (!container) {
        return;
    }

    let canvaElement = container.querySelector('canvas');

    if (!canvaElement) {
        return;
    }

    if (debugIsOn()) {
        globalThis._mt_ob = {
            canvaElement,
            container
        };
    }

    let isDown = false;
    let scrollTop = 0;
    let startX, startY, lastX, lastY, lastTime;

    container.addEventListener('mousedown', e => {
        debugLog('mousedown');
        container.classList.add('active');
        isDown = true;
        startY = e.pageY - container.offsetTop;
        scrollTop = container.scrollTop;
        startX = e.pageX;
        startY = e.pageY;

        lastX = startX;
        lastY = startY;
        lastTime = Date.now();
    });

    // container.addEventListener('mouseleave', () => {
    //   debugLog('mouseleave');
    //   isDown = false;
    //   container.classList.remove('active');
    // });

    container.addEventListener('mouseup', () => {
        debugLog('mouseup');
        isDown = false;
        container.classList.remove('active');
    });

    const getDirection = moveEvent => {
        const deltaX = moveEvent.pageX - startX; // Diferença em X
        const deltaY = moveEvent.pageY - startY; // Diferença em Y

        // Determina a direção do arraste
        if (Math.abs(deltaX) > Math.abs(deltaY)) {
            return deltaX > 0 ? 'RIGHT' : 'LEFT';
        }

        return deltaY > 0 ? 'DOWN' : 'UP';
    };

    const getSpeed = moveEvent => {
        const currentX = moveEvent.pageX;
        const currentY = moveEvent.pageY;
        const currentTime = Date.now();

        // Calcula a distância percorrida
        const distanceX = currentX - lastX;
        const distanceY = currentY - lastY;

        // Calcula o tempo decorrido
        const timeElapsed = currentTime - lastTime;

        // Calcula a velocidade
        const speed =
            Math.sqrt(distanceX * distanceX + distanceY * distanceY) /
            timeElapsed;

        // Atualiza as últimas posições e tempo
        lastX = currentX;
        lastY = currentY;
        lastTime = currentTime;

        // Define um limite para classificar a velocidade
        if (speed > 0.5) {
            // Ajuste esse valor conforme necessário
            debugLog('Arraste rápido');

            return 'FAST';
        }

        debugLog('Arraste devagar');
        return 'SLOW';
    };

    const scrollDoc = newPosition => {
        newPosition = newPosition <= 0 ? 0 : newPosition;
        newPosition =
            newPosition >= canvaElement?.offsetHeight
                ? canvaElement?.offsetHeight
                : newPosition;
        container.scrollTop = newPosition <= 0 ? 0 : newPosition;
    };

    container.addEventListener('mousemove', e => {
        if (!isDown) {
            return; // Se não estiver pressionado, não faz nada
        }

        e.preventDefault();
        const y = e.pageY - container.offsetTop;
        const walk = (y - startY) * 2; // Aumenta a velocidade do scroll

        //   acho que eu quero e.layerY (parece que e.pageY também serve)
        debugLog(
            'mousemove',
            `isDown: ${isDown}
            container.offsetTop: ${container.offsetTop}
            e.pageY: ${e.pageY}

            y: ${y}
            walk: ${walk};
            `,
            e
        );

        let direction = getDirection(e);
        let speed = getSpeed(e);

        debugLog(direction);

        if (!['UP', 'DOWN'].includes(direction)) {
            return;
        }

        // let toCalc = y;
        let toCalc = speed === 'FAST' ? 20 : 5.5;
        let naturalScroll = false; // Depois permitir seleçãopelo usuário

        let newValue =
            direction === (naturalScroll ? 'DOWN' : 'UP')
                ? container.scrollTop + toCalc
                : container.scrollTop - toCalc;

        scrollDoc(newValue);
    });

    document.querySelector('body')?.addEventListener('keydown', event => {
        let actionCode = event.key || event.code || event?.detail?.code;

        if (!actionCode) {
            return;
        }

        if (['ArrowUp', 'ArrowDown'].includes(actionCode)) {
            let toCalc = event?.shiftKey ? 5 : 15;

            if (actionCode === 'ArrowUp') {
                scrollDoc(container?.scrollTop - toCalc);
                return;
            }

            scrollDoc(container?.scrollTop + toCalc);
            return;
        }

        if (['ArrowRight', 'ArrowLeft'].includes(actionCode)) {
            // let toCalc = event?.shiftKey ? 5 : 15;

            if (actionCode === 'ArrowRight') {
                document.dispatchEvent(
                    new CustomEvent('page:goto', { detail: 'next' })
                );
                return;
            }

            document.dispatchEvent(
                new CustomEvent('page:goto', { detail: 'prev' })
            );
            return;
        }

        if (
            [
                '+',
                '-',
                '0',
                '1',
                '2',
                '3',
                '4',
                '5',
                '6',
                '7',
                '8',
                '9'
            ].includes(actionCode)
        ) {
            debugLog(`WIDTH ACTION actionCode ${actionCode}`);

            let newWidth = Number(actionCode === '0' ? 100 : `${actionCode}0`);

            let currentWidth = globalThis._mt_ob.container?.style?.width || '';
            currentWidth = Number(currentWidth.replace(/\D/g, ''));

            if (['-', '+'].includes(actionCode)) {
                newWidth =
                    actionCode === '+' ? currentWidth + 10 : currentWidth - 10;
            }

            newWidth = newWidth <= 10 ? 10 : newWidth;

            setContainerWidth(newWidth);
            return;
        }

        debugLog(`actionCode ${actionCode}`, event);
    });

    const initZoomValue = localStorage.getItem('zoom_level') ?? CONFIG?.defaultZoom ?? 50;

    if (initZoomValue) {
        setContainerWidth(initZoomValue);
    }
};

document.addEventListener('DOMContentLoaded', event => {
    init('body div');
});
