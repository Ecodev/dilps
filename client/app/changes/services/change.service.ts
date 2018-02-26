import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import { AbstractModelService } from '../../shared/services/abstract-model.service';
import { acceptChange, changeQuery, changesQuery, rejectChange } from './changeQueries';
import { ChangesQuery } from '../../shared/generated-types';
import { map } from 'rxjs/operators';

@Injectable()
export class ChangeService
    extends AbstractModelService<null,
        ChangesQuery['changes'],
        null,
        null,
        null> {

    constructor(apollo: Apollo) {
        super(apollo,
            'change',
            changeQuery,
            changesQuery, null, null, null);
    }

    public acceptChange(change: { id }) {
        return this.apollo.mutate({
            mutation: acceptChange,
            variables: {
                id: change.id,
            },
            refetchQueries: this.getRefetchQueries(),
        }).pipe(map(data => data.data.acceptChange));
    }

    public rejectChange(change: { id }) {
        return this.apollo.mutate({
            mutation: rejectChange,
            variables: {
                id: change.id,
            },
            refetchQueries: this.getRefetchQueries(),
        }).pipe(map(data => data.data.rejectChange));
    }

}
