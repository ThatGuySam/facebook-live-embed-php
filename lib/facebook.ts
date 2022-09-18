import axios from 'axios'

import { CheckLive } from './check-live'


const favebookLiveUrl = 'https://m.facebook.com/watch/live/?ref=live_bookmark&zero_e=1&zero_et=1663516225&_rdc=1&_rdr&refsrc=deprecated'


function getHrefs ( html:string ) {
    const hrefs:Set<string> = new Set()
    const re = /href="([^"]+)"/g
    // /href="(.*?)"/g
    let match
    while ( match = re.exec( html ) ) {
        hrefs.add( match[1] )
    }
    return hrefs
}

function findSampleLivePages ( html:string ) {
    const hrefs = getHrefs( html )
    const livePages:Map<string, string> = new Map()

    for ( const href of hrefs ) {

        // Skip urls that start with /watch
        if ( href.startsWith( '/watch/' ) ) continue

        // Urls without ?ref=live_bookmark
        if ( !href.includes( '?ref=live_bookmark' ) ) continue

        const pageUrl = new URL( href, 'https://m.facebook.com' )

        // Get path without trailing slash
        const cleanPath = pageUrl.pathname.replace(/\/$/, '')

        // Skip urls that don;t have a single segment
        if ( cleanPath.split( '/' ).length !== 2 ) continue

        const pageSlug = cleanPath.split( '/' )[1]

        livePages.set( pageUrl.href, pageSlug )
    }

    return livePages
}

export async function getRandomFacebookLivePageUrls () {
    const response = await axios.get( favebookLiveUrl )

    // console.log( 'response.data', response.data )

    return findSampleLivePages( response.data )
}
export class FacebookCheckLive extends CheckLive {

    async checkLive ( name, identifier ) {

        // const response = await youTubeAxios.head( url, {
        //     headers: { 'user-agent': pixelUA }
        // } )
        // .catch( ( error ) => {
        //     // console.log( 'error', error )

        //     return error.response
        // } )

        return [ 'works' ]
    }
}