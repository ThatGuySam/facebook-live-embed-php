import https from 'https'
import axios from 'axios'
import { registerInterceptor } from 'axios-cached-dns-resolve'
import httpTimer from '@szmarczak/http-timer'

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