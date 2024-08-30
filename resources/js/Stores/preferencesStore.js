import { inject, ref, computed } from 'vue'
import { defineStore, skipHydrate } from 'pinia'
import { useLocalStorage } from '@vueuse/core'

export const useAuthStore = defineStore('auth', () => {
    const appProvided = inject('appProvided')

    return {
        state: () => ({
            user: useLocalStorage('pinia/auth/login', 'bob')
        }),

        hydrate (state, initialState) {
            // in this case we can completely ignore the initial state since we
            // want to read the value from the browser
            state.user = useLocalStorage('pinia/auth/login', 'bob')
        }
    }
})

const colorSchemaIsDark = () => {
    return window.matchMedia('(prefers-color-scheme: dark)').matches
}

const themeBySchema = () => {
    return colorSchemaIsDark() ? 'dark' : 'light'
}

export const _useThemeStore = defineStore('theme', () => {
    return {
        state: () => ({
            theme: useLocalStorage('theme', 'dark'),

            get defa () {
                return 'dark'
            }
        }),
        get defa2 () {
            return 'dark'
        },

        actions: {
            toggleTheme () {
                this.theme = this.theme === 'light' ? 'dark' : 'light'
            },

            bySchema () {
                return this.schemaIsDark() ? 'dark' : 'light'
            },

            schemaIsDark () {
                return colorSchemaIsDark()
            },

            get defa3 () {
                return 'dark'
            },

            colorSchemaIsDark
        }
    }
})

export const useCounterStore = defineStore('counter', {
    state: () => ({
        count: useLocalStorage('count', 0)
    }),
    actions: {
        increment () {
            console.log('increment');
            this.count++
        }
    }
})

export const useThemeStore = defineStore('theme', {
    state: () => ({
        theme: useLocalStorage('color-theme', themeBySchema())
    }),
    actions: {
        toggleTheme () {
            // console.log('toggleTheme');
            this.theme = this.theme === 'light' ? 'dark' : 'light';
            this.updateRootDocument();
        },
        systemTheme () {
            // console.log('systemTheme');
            this.theme = this.themeBySchema();
            this.updateRootDocument();
        },
        updateRootDocument() {
            // console.log('updateRootDocument');
            let theme = (this.theme || this.themeBySchema) || 'dark';
            let isDark = theme === 'dark';
            let method = isDark ? 'add' : 'remove';
            let rootDocument = document?.querySelector('body');

            if (!rootDocument || !rootDocument?.classList[method]) {
                return;
            }

            rootDocument?.classList[method]('theme-dark');
        },
        themeBySchema,
        colorSchemaIsDark,
    }
})
