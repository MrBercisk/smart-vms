const API_BASE = '/api'

function getCookie(name) {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'))
    return match ? decodeURIComponent(match[2]) : null
}

async function request(method, url, body = null, isFormData = false) {
    const headers = {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    }

    const xsrfToken = getCookie('XSRF-TOKEN')
    if (xsrfToken) {
        headers['X-XSRF-TOKEN'] = xsrfToken
    }

    let fetchBody = null
    if (body) {
        if (isFormData || body instanceof FormData) {
            fetchBody = body
        } else {
            headers['Content-Type'] = 'application/json'
            fetchBody = JSON.stringify(body)
        }
    }

    const res = await fetch(API_BASE + url, {
        method,
        credentials: 'same-origin',
        headers,
        body: fetchBody,
    })

    let data = null
    const contentType = res.headers.get('content-type') || ''
    if (contentType.includes('application/json')) {
        data = await res.json()
    }

    if (!res.ok) {
        const error = new Error(data?.message || 'Request failed')
        error.response = { status: res.status, data }
        throw error
    }

    return { data, status: res.status }
}

export default {
    get:    (url) => request('GET', url),
    post:   (url, body, isFormData = false) => request('POST', url, body, isFormData),
    put:    (url, body) => request('PUT', url, body),
    patch:  (url, body) => request('PATCH', url, body),
    delete: (url) => request('DELETE', url),
}