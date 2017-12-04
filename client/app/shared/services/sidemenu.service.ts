import { Injectable } from '@angular/core';
import { ObservableMedia } from '@angular/flex-layout';

/**
 * @Todo : Fix nav minimize and maximize resize
 * Since Material 2 beta 10, when nav is resized the body is not resized
 * https://github.com/angular/material2/issues/6743
 * Maybe the better is to wait next release
 */
@Injectable()
export class SidemenuService {

    /**
     * Navigation modes
     * First is for desktop view
     * Second is for mobile view
     * @type {[string , string]}
     */
    private modes = [
        'side',
        'push',
    ];

    /**
     * Activated mode
     * Default to desktop view
     * @type {string}
     */
    private mode = this.modes[0];

    /**
     * Wherever is nav is opened or not
     * @type {boolean}
     */
    private opened = true;

    /**
     * Stores the opened status during mobile view, to restore if we come back to desktop view
     * @type {boolean}
     */
    private tmpOpened: boolean = this.opened;

    /**
     * Wherever is nav is minimized or not
     * @type {boolean}
     */
    private minimized = false;

    /**
     * LocalStorage key that stores the minized status
     * @type {string}
     */
    private minimizedStorageKey = 'menu-minimized';

    /**
     * LocalStorage key that stores the opened status
     * @type {string}
     */
    private openedStorageKey = 'menu-opened';

    constructor(public media: ObservableMedia) {

        // Init from LocalStorage
        this.minimized = this.getMinimizedStatus();
        this.opened = this.getMenuOpenedStatus();
        this.tmpOpened = this.opened;

        let oldIsBig = null;
        media.subscribe(() => {

            const isBig = !this.isMobileView();
            this.mode = isBig ? this.modes[0] : this.modes[1];

            if (oldIsBig === null || isBig !== oldIsBig) {

                oldIsBig = isBig;

                // If decrease window size, save status of menu before closing it
                if (!isBig) {
                    this.tmpOpened = this.opened;
                    this.opened = false;
                }

                // If increase window size, and sidebar was open before, re-open it.
                if (isBig && this.tmpOpened) {
                    this.opened = true;
                    this.minimized = this.getMinimizedStatus();
                }
            }
        });
    }

    get activeMode(): string {
        return this.mode;
    }

    get isOpened(): boolean {
        return this.opened;
    }

    get isMinimized(): boolean {
        return this.minimized;
    }

    public isMobileView() {
        return !this.media.isActive('gt-sm');
    }

    /**
     * Close nav on mobile view after a click
     */
    public navItemClicked() {
        if (this.isMobileView()) {
            this.toggle();
        }
    }

    /**
     * Change minimized status and stores the new value
     * @param val
     */
    public setMinimized(val) {
        this.minimized = val;
        localStorage.setItem(this.minimizedStorageKey, val);
    }

    /**
     * Get the stored minimized status
     * @returns {boolean}
     */
    public getMinimizedStatus() {
        return localStorage.getItem(this.minimizedStorageKey) === 'true';
    }

    /**
     * Get the stored opened status
     * @returns {boolean}
     */
    public getMenuOpenedStatus() {
        return localStorage.getItem(this.openedStorageKey) === null || localStorage.getItem(this.openedStorageKey) === 'true';
    }

    /**
     * Toggle menu but expand it if mobile mode is activated
     * Stores the status in local storage
     */
    public toggle() {
        this.opened = !this.opened;

        if (this.isMobileView()) {
            this.minimized = false;
        } else {
            localStorage.setItem(this.openedStorageKey, this.opened ? 'true' : 'false');
        }

    }

}
