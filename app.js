var ws = new WebSocket('ws://192.168.43.205:5000/');
ws.onopen = function(open) {
    console.log("Connection established!");
    var string = 'asdasdasd';
    ws.send(string);
    console.log(string.length);
};

ws.onmessage = function(message) {
    console.log(message);
    document.write("", message.data);
};

ws.onerror = function(error){
    console.log(error);
}

ws.onclose = function(close){
    console.log(close);
}
