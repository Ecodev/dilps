import { Injectable } from '@angular/core';
import { isUndefined, mapValues, trim } from 'lodash';

export interface Address {
    street?: string;
    postcode?: string;
    locality?: string;
    area?: string;
    country?: string;
    latitude?: number;
    longitude?: number;
}

@Injectable()
export class AddressService {

    /**
     * Binds gmap semantic with string we should retrieve
     */
    private config = {
        route: 'long_name',
        street_number: 'short_name',
        postal_code: 'short_name',
        locality: 'long_name',
        country: 'short_name',
    };

    constructor() {
    }

    public buildAddress(place: any) {

        const tmpGAddress = mapValues(this.config, function() {
            return '';
        });

        place.address_components.forEach((addressComponent: any) => {
            const addressType = addressComponent.types[0];
            if (!isUndefined(this.config[addressType])) {
                tmpGAddress[addressType] = addressComponent[this.config[addressType]];
            }
        });

        const address = {
            street: trim(tmpGAddress.route + ' ' + tmpGAddress.street_number),
            postcode: tmpGAddress.postal_code,
            locality: tmpGAddress.locality,
            countryIso2: tmpGAddress.country,
            latitude: place.geometry.location.lat(),
            longitude: place.geometry.location.lng(),
        };

        return address;
    }

    public toString(address: Address) {
        let text = '';
        text += address.street ? address.street : '';
        text += address.postcode ? ' ' + address.postcode : '';
        text += address.locality ? ' ' + address.locality : '';
        text += address.area ? ' ' + address.area : '';
        text += address.country ? ' ' + address.country : '';
        return text;
    }

}
