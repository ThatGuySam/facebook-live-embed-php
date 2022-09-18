import https from 'https'
import axios, { AxiosResponse } from 'axios'
import { registerInterceptor } from 'axios-cached-dns-resolve'
import httpTimer from '@szmarczak/http-timer'

import { CheckLive } from './check-live'

const youTubeAxiosConfig:any = {
    // transport: {
    //     // request: function httpsWithTimer(...args) {
    //     //     const request = https.request.apply(null, args)
    //     //     httpTimer(request)
    //     //     return request
    //     // },
    //     // response: function httpsWithTimer(...args) {
    //     //     const response = https.request.apply(null, args)
    //     //     httpTimer(response)
    //     //     return response
    //     // }
    // }
}

export const youTubeAxios = axios.create( youTubeAxiosConfig )

// Setup the interceptor
// so that DNS for our subsequent requests will be cached
registerInterceptor(youTubeAxios)

const pixelUA = 'Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.91 Mobile Safari/537.36'

const liveIgnoredValues = new Set([
    'VISITOR_INFO1_LIVE',
    'YSC',
    'Expires',
    'date'
])


// Keys for data that varies between livestream headers
const liveIgnoredIndexes = new Set([
    'report-to',
    'cross-origin-opener-policy-report-only',
    'permissions-policy',
    'x-frame-options',
    'strict-transport-security'
])

export function makeYouTubeUrl ( parts:any ) {
    // return `https://m.youtube.com/channel/${ youtubeId }/live`

    const urlBase = parts?.base || 'https://m.youtube.com'

    const url = new URL( '', urlBase )

    for ( const [ key, value ] of Object.entries( parts ) ) {
        url[key] = value
    }

    return url.toString()
}

function parseCookieValues ( value: Array<string> ) {
    return value.map( ( cookie: string ) => {
        // Split by semicolons
        const cookieParts = cookie.split( ';' )

        // Map the parts into an array of key value pairs
        const mappedCookieParts = cookieParts.map( ( part: string ) => {
            const [ key, value ] = part.split( '=' ).map( ( part: string ) => part.trim() )

            if ( liveIgnoredValues.has( key ) ) {
                return [ key, 'ignored' ]
            }

            return [ key, value ]
        } )

        return mappedCookieParts
    } )
}

export function parseResponseParts ( response: AxiosResponse, identifier: string = 'no identifier' ) {

    const headerEntries = Object.entries( response.headers )

    const responseHeaders = headerEntries.map( ( [ key, initialValue ]:any, entryIndex ) => {

        const index = liveIgnoredIndexes.has( key ) ? 'ignored' : entryIndex
        const value = liveIgnoredValues.has( key ) ? 'ignored' : initialValue

        // If the header value is set-cookie then map it into an array
        // so that we can compare them and omit data only specfic to this request
        if ( key === 'set-cookie' ) {
            
            const parsedCookies = parseCookieValues( value )

            return [ key, { index, value: parsedCookies } ]
        }


        // If the header is content-length then round it to the nearest 100
        if ( key === 'content-length' ) {

            // const nearest = 100_000
            // const roundedValue = Math.round( Number( value ) / nearest )// * nearest

            return [ key, { index, value: 'TODO' } ]
        }

        if ( key === 'location' ) {
            return [ key, { index, value: 'TODO' } ]
        }

        return [ key, { index, value } ]
    } )

    // console.log( 'fetchedUrls', response.request.res.responseUrl )
    // console.log( 'count', response.request._redirectable._redirects )
    
    return {
        headers: Object.fromEntries( responseHeaders ),
        // redirectCount: response.request._redirectable._redirectCount,
        // redirects: response.request._redirectable._redirects,
        // responseUrl: normalResponseUrl( response.request.res.responseUrl, [ identifier ] ),
        // fetchedUrls: response.request.res.fetchedUrls,
    }
}

export class YouTubeCheckLive extends CheckLive {

    async checkLive ( name, identifier ) {
        const url = makeYouTubeUrl( { 
            base: 'https://www.youtube.com',

            pathname: `/channel/${ identifier }/live`,

            // /channel/UCHd62-u_v4DvJ8TCFtpi4GA/videos?view=2&sort=dd&live_view=501&shelf_id=0
            // pathname: `/channel/${ identifier }/videos`,
            // search: 'view=2&sort=dd&live_view=501&shelf_id=0'


            // pathname: `/c/${ identifier }/live`,

            // pathname: `/${ identifier }/live`,
        } )


        console.log( 'fetching', name, url )

        const response = await youTubeAxios.head( url, {
            headers: { 'user-agent': pixelUA }
        } )
        .catch( ( error ) => {
            // console.log( 'error', error )

            return error.response
        } )

        return parseResponseParts( response, identifier )
    }

}