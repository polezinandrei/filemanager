document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(event) {
        const target = event.target;
        if (target.tagName == 'A' && target.classList.contains('element')) {
            if (target.dataset.dir == 'true') {
                event.preventDefault();
                directoryNavigate(target);
            }
        }
        if (target.tagName == 'BUTTON' && target.classList.contains('remove')) {
            target.addEventListener('click', removeElement(target));
        }
        if (target.tagName == 'BUTTON' && target.classList.contains('rename')) {
            target.addEventListener('click', renameElement(target));
        }
    });

    const createDir = document.querySelector('.create-dir');
    createDir.addEventListener('click', () => directoryCreate(prompt('Название папки', 'Новая папка')));
});

function renameElement(element) {
    const name = prompt('Введите название.', 'Новое имя');
    const requestURI = '/index.php';
    const method = 'POST';
    const curDir = document.querySelector('.current-dir').textContent;
    const headers = {"Content-Type": "application/json"};
    const data = {type: 'rename', name: name, element: element.dataset.path, path: curDir};
    sendData(requestURI, method, data, headers);
}

function removeElement(element) {
    if (confirm(`Хотите удалить?`)) {
        const requestURI = '/index.php';
        const method = 'POST';
        const curDir = document.querySelector('.current-dir').textContent;
        const headers = {"Content-Type": "application/json"};
        const data = {type: 'delete', dir: element.dataset.dir, element: element.dataset.path, path: curDir};
        sendData(requestURI, method, data, headers);
    }

    return;
}

function directoryCreate(dirName) {
    const requestURI = '/index.php';
    const method = 'POST';
    const curDir = document.querySelector('.current-dir').textContent;
    const data = {type: 'create', name: dirName, path: curDir};
    const headers = {"Content-Type": "application/json"};
    sendData(requestURI, method, data, headers);
}

function directoryNavigate(target) {
    const requestURI = '/index.php';
    const method = 'POST';
    const data = {path: target.dataset.path};
    const headers = {"Content-Type": "application/json"};
    sendData(requestURI, method, data, headers);
}

function sendData(url, method, data = null, headers = {}) {
    fetch(url, {
        method: method,
        body: JSON.stringify(data),
        headers: headers,
    })
    .then(response => response.json())
    .then(data => renderResponse(data))
    .catch(err => console.error(err));
};

const renderResponse = response => {
    const list = document.querySelector('.list');
    let html = '';
    const currentDir = document.querySelector('.current-dir');
    const status = document.querySelector('.status');
    status.innerHTML = '';
    const statusMessage = response['status'];

    if (statusMessage.message != undefined)
        status.innerHTML = statusMessage.message;
    if (statusMessage.err == '1') {
        status.classList.add('error');
    } else {
        status.classList.remove('error');
    }

    response['list'].forEach((item, index) => {
        const dirClass = item.dir == 'true' ? 'dir' : 'file';
        const download = item.dir == 'false' ? 'download' : '';
        html += `<div class="line">
                    <a class="element ${dirClass}" ${download} data-dir="${item.dir}" data-path="${item.path}" href="${item.url}">${item.name}</a>
                    <p>
                        <button class="rename ${dirClass}" data-dir="${item.dir}" data-path="${item.path}">Переименовать</button>
                        <button class="remove ${dirClass}" data-dir="${item.dir}" data-path="${item.path}">Удалить</button>                                          
                    </p>
                </div>`;
        currentDir.innerHTML = `${item.current}`;
        document.querySelector('input[type=hidden]').value = `${item.current}`;
    });
    list.innerHTML = html;
}