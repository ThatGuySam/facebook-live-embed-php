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

registerInterceptor(youTubeAxios)

export function makeYouTubeUrl ( parts:any ) {
    // return `https://m.youtube.com/channel/${ youtubeId }/live`

    const urlBase = parts?.base || 'https://m.youtube.com'

    const url = new URL( '', urlBase )

    for ( const [ key, value ] of Object.entries( parts ) ) {
        url[key] = value
    }

    return url.toString()
}