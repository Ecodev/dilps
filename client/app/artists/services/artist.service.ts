import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import {
    createArtistMutation,
    deleteArtistMutation,
    artistQuery,
    artistsQuery,
    updateArtistMutation,
} from './artistQueries';
import 'rxjs/add/observable/of';
import {
    CreateArtistMutation,
    DeleteArtistMutation,
    ArtistQuery,
    ArtistsQuery,
    UpdateArtistMutation,
} from '../../shared/generated-types';
import { AbstractModelService } from '../../shared/services/abstract-model.service';

@Injectable()
export class ArtistService
    extends AbstractModelService<ArtistQuery['artist'],
        ArtistsQuery['artists'],
        CreateArtistMutation['createArtist'],
        UpdateArtistMutation['updateArtist'],
        DeleteArtistMutation['deleteArtist']> {

    constructor(apollo: Apollo) {
        super(apollo,
            'artist',
            artistQuery,
            artistsQuery,
            createArtistMutation,
            updateArtistMutation,
            deleteArtistMutation);
    }

    public getEmptyObject() {
        return {
            name: '',
        };
    }

}
