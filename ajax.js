// takes a {} object and returns a FormData object
var objectToFormData = function (obj, form, namespace) {
    var fd = form || new FormData();
    var formKey;

    for (var property in obj) {
        if (obj.hasOwnProperty(property)) {
            if (namespace) {
                formKey = namespace + '[' + property + ']';
            } else {
                formKey = property;
            }
            // if the property is an object, but not a File,
            // use recursivity.
            if (typeof obj[property] === 'object' && !(obj[property] instanceof File)) {
                objectToFormData(obj[property], fd, property);
            } else {
                // if it's a string or a File object
                fd.append(formKey, obj[property]);
            }
        }
    }

    return fd;
};

// 반환값이 json이 아니면 텍스트 및 스크립트로 처리하며 실패로 간주.
export default function (url, data) {
    return new Promise(async (resolve, reject) => {
        try {
            const options = {}
            if (data) {
                options.method = 'POST'
                options.body = objectToFormData(data)
            }
            const r = await fetch(url, options)
            try {
                const j = await r.clone().json()
                resolve(j)
            } catch (e) {
                const t = await r.text()
                new DOMParser().parseFromString(t, 'text/html').body.querySelectorAll('script')
                    .forEach(s => eval(s.innerText))
                reject(t)
            }
        } catch (e) {
            reject(e)
        }
    })
}
