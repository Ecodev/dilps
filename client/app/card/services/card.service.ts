import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import 'rxjs/add/observable/of';
import { merge } from 'lodash';
import {
    CardInput,
    CardQuery,
    CardsQuery,
    CardStatus,
    CreateCardMutation,
    DeleteCardsMutation,
    UpdateCardMutation,
} from '../../shared/generated-types';
import { AbstractModelService } from '../../shared/services/abstract-model.service';
import { cardQuery, cardsQuery, createCardMutation, deleteCardsMutation, updateCardMutation, } from './cardQueries';
import { Literal } from '../../shared/types';

@Injectable()
export class CardService extends AbstractModelService<CardQuery['card'],
    CardsQuery['cards'],
    CreateCardMutation['createCard'],
    UpdateCardMutation['updateCard'],
    DeleteCardsMutation['deleteCards']> {

    public static getImageFormat(card, height): any {
        height = card.height ? Math.min(card.height, height) : height;
        const ratio = card.width / card.height;
        return {
            height: height,
            width: height * ratio,
        };
    }

    public static getImageLink(card, height) {
        if (!card || !card.id || !card.hasImage) {
            return null;
        }

        const imageLink = '/image/' + card.id;
        if (!height) {
            return imageLink;
        }

        const size = this.getImageFormat(card, height);
        return imageLink + '/' + size.height;
    }

    /**
     * Merge image src on src attribute of given gard
     * @param card
     * @param height
     * @returns {{} & any & {src: *}}
     */
    public static formatImage(card, height) {
        if (!card) {
            return null;
        }

        const fields = {src: this.getImageLink(card, height)};
        return merge({}, card, fields);
    }

    constructor(apollo: Apollo) {
        super(apollo, 'card', cardQuery, cardsQuery, createCardMutation, updateCardMutation, deleteCardsMutation);
    }

    public getEmptyObject(): CardInput {
        return {
            file: null,
            dating: '',
            status: CardStatus.new,
            addition: '',
            expandedName: '',
            material: '',
            technique: '',
            techniqueAuthor: '',
            format: '',
            literature: '',
            page: '',
            figure: '',
            table: '',
            isbn: '',
            comment: '',
            rights: '',
            muserisUrl: '',
            muserisCote: '',
            name: '',
            isPublic: false,
            artists: [],
            institution: null,
            street: '',
            postcode: '',
            locality: '',
            area: '',
            latitude: null,
            longitude: null,
            country: null,
            original: null,
        };
    }

    protected getInput(object: Literal): Literal {

        const input = super.getInput(object);

        // If file is undefined or null, prevent to send attribute to server
        if (!object.file) {
            delete input.file;
        }

        return input;
    }

}
