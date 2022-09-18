

export function makeYouTubeUrl ( parts:any ) {
    // return `https://m.youtube.com/channel/${ youtubeId }/live`

    const urlBase = parts?.base || 'https://m.youtube.com'

    const url = new URL( '', urlBase )

    for ( const [ key, value ] of Object.entries( parts ) ) {
        url[key] = value
    }

    return url.toString()
}