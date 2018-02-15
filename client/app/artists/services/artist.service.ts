import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import {
    createArtistMutation,
    deleteArtistsMutation,
    artistQuery,
    artistsQuery,
    updateArtistMutation,
} from './artistQueries';
import 'rxjs/add/observable/of';
import {
    CreateArtistMutation,
    DeleteArtistsMutation,
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
        DeleteArtistsMutation['deleteArtists']> {

    constructor(apollo: Apollo) {
        super(apollo,
            'artist',
            artistQuery,
            artistsQuery,
            createArtistMutation,
            updateArtistMutation,
            deleteArtistsMutation);
    }

    public getEmptyObject() {
        return {
            name: '',
        };
    }

}
