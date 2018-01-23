/**
 * Created by Tiny on 2018/1/18.
 */
/**
 * Created by louizhai on 17/6/30.
 * description: Use canvas to draw.
 * draw.js的es5写法
 */
function Draw(canvas, degree) {
    var config = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};

    if (!(this instanceof Draw)) {
        return new Draw(canvas, config);
    }
    if (!canvas) {
        return;
    }

    var _window$getComputedSt = window.getComputedStyle(canvas, null),
        width = _window$getComputedSt.width,
        height = _window$getComputedSt.height;

    width = width.replace('px', '');
    height = height.replace('px', '');

    this.canvas = canvas;
    this.context = canvas.getContext('2d');
    this.width = width;
    this.height = height;
    var context = this.context;

    // 根据设备像素比优化canvas绘图
    var devicePixelRatio = window.devicePixelRatio;
    if (devicePixelRatio) {
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        canvas.height = height * devicePixelRatio;
        canvas.width = width * devicePixelRatio;
        context.scale(devicePixelRatio, devicePixelRatio);
    } else {
        canvas.width = width;
        canvas.height = height;
    }


    //===========================
    //函数名:  isEmptyObject
    //功能:    判断对象是否为空
    //输入参数: obj  被判断的对象 ;
    //返回值:  true or false
    //============
     var isEmptyObject = function(obj) {
        for(var key in obj) {
            return false
        };
        return true
    };
    //===========================
    //函数名:  mergeObj
    //功能:    合并对象，数组追加数据
    //输入参数: source  原对象 ;
    //输入参数: target  被合并的对象;
    //返回值:  新的对象
    //============
     var mergeObj = function(source, target) {
        var i;
        if(isEmptyObject(source)){     //判断对象或者数组是否为空
            return target;
        }else if(target.length==0){       //判断被合并的数据长度是否为0
            return source;
        }
        try{
            if(target.constructor == Array){    //数组追加
                for ( i in target) {
                    source.push(target[i]);
                }
            }else if(target.constructor == Object){   //对象合并
                for ( i in target) {
                    source[i] = target[i];
                }
            }
        }catch(e){          //报错返回第二个
            return target;
        }
        return source;
    }

    context.lineWidth = 6;
    context.strokeStyle = 'black';
    context.lineCap = 'round';
    context.lineJoin = 'round';
    //Object.assign(context, config); //这个是es6的语法，很多手机不支持
    mergeObj(context, config);

    var _canvas$getBoundingCl = canvas.getBoundingClientRect(),
        left = _canvas$getBoundingCl.left,
        top = _canvas$getBoundingCl.top;

    var point = {};
    var isMobile = /phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone/i.test(navigator.userAgent);
    // 移动端性能太弱, 去掉模糊以提高手写渲染速度
    if (!isMobile) {
        context.shadowBlur = 1;
        context.shadowColor = 'black';
    }
    var pressed = false;

    var paint = function paint(signal) {
        switch (signal) {
            case 1:
                context.beginPath();
                context.moveTo(point.x, point.y);
            case 2:
                context.lineTo(point.x, point.y);
                context.stroke();
                break;
            default:
        }
    };
    var create = function create(signal) {
        return function (e) {
            e.preventDefault();
            if (signal === 1) {
                pressed = true;
            }
            if (signal === 1 || pressed) {
                e = isMobile ? e.touches[0] : e;
                point.x = e.clientX - left;
                point.y = e.clientY - top;
                paint(signal);
            }
        };
    };
    var start = create(1);
    var move = create(2);
    var requestAnimationFrame = window.requestAnimationFrame;
    var optimizedMove = requestAnimationFrame ? function (e) {
        requestAnimationFrame(function () {
            move(e);
        });
    } : move;

    if (isMobile) {
        canvas.addEventListener('touchstart', start);
        canvas.addEventListener('touchmove', optimizedMove);
    } else {
        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', optimizedMove);
        ['mouseup', 'mouseleave'].forEach(function (event) {
            canvas.addEventListener(event, function () {
                pressed = false;
            });
        });
    }

    // 重置画布坐标系
    if (typeof degree === 'number') {
        this.degree = degree;
        context.rotate(degree * Math.PI / 180);
        switch (degree) {
            case -90:
                context.translate(-height, 0);
                break;
            case 90:
                context.translate(0, -width);
                break;
            case -180:
            case 180:
                context.translate(-width, -height);
                break;
            default:
        }
    }
}
Draw.prototype = {
    scale: function scale(width, height) {
        var canvas = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : this.canvas;

        var w = canvas.width;
        var h = canvas.height;
        width = width || w;
        height = height || h;
        if (width !== w || height !== h) {
            var tmpCanvas = document.createElement('canvas');
            var tmpContext = tmpCanvas.getContext('2d');
            tmpCanvas.width = width;
            tmpCanvas.height = height;
            tmpContext.drawImage(canvas, 0, 0, w, h, 0, 0, width, height);
            canvas = tmpCanvas;
        }
        return canvas;
    },
    rotate: function rotate(degree) {
        var image = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : this.canvas;

        degree = ~~degree;
        if (degree !== 0) {
            var maxDegree = 180;
            var minDegree = -90;
            if (degree > maxDegree) {
                degree = maxDegree;
            } else if (degree < minDegree) {
                degree = minDegree;
            }

            var canvas = document.createElement('canvas');
            var context = canvas.getContext('2d');
            var height = image.height;
            var width = image.width;
            var degreePI = degree * Math.PI / 180;

            switch (degree) {
                // 逆时针旋转90°
                case -90:
                    canvas.width = height;
                    canvas.height = width;
                    context.rotate(degreePI);
                    context.drawImage(image, -width, 0);
                    break;
                // 顺时针旋转90°
                case 90:
                    canvas.width = height;
                    canvas.height = width;
                    context.rotate(degreePI);
                    context.drawImage(image, 0, -height);
                    break;
                // 顺时针旋转180°
                case 180:
                    canvas.width = width;
                    canvas.height = height;
                    context.rotate(degreePI);
                    context.drawImage(image, -width, -height);
                    break;
                default:
            }
            image = canvas;
        }
        return image;
    },
    getPNGImage: function getPNGImage() {
        var canvas = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this.canvas;

        return canvas.toDataURL('image/png');
    },
    getJPGImage: function getJPGImage() {
        var canvas = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this.canvas;

        return canvas.toDataURL('image/jpeg', 0.5);
    },
    downloadPNGImage: function downloadPNGImage(image) {
        var url = image.replace('image/png', 'image/octet-stream;Content-Disposition:attachment;filename=test.png');
        window.location.href = url;
    },
    dataURLtoBlob: function dataURLtoBlob(dataURL) {
        var arr = dataURL.split(',');
        var mime = arr[0].match(/:(.*?);/)[1];
        var bStr = atob(arr[1]);
        var n = bStr.length;
        var u8arr = new Uint8Array(n);
        while (n--) {
            u8arr[n] = bStr.charCodeAt(n);
        }
        return new Blob([u8arr], { type: mime });
    },
    clear: function clear() {
        var width = void 0;
        var height = void 0;
        switch (this.degree) {
            case -90:
            case 90:
                width = this.height;
                height = this.width;
                break;
            default:
                width = this.width;
                height = this.height;
        }
        this.context.clearRect(0, 0, width, height);
    },
    upload: function upload(image, blob, url, success, failure) {
        var formData = new FormData();
        var xhr = new XMLHttpRequest();
        xhr.withCredentials = true;
        formData.append('image', image);

        xhr.open('POST', url, true);
        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300 || xhr.status === 304) {
                success(xhr.responseText);
            } else {
                failure();
            }
        };
        xhr.onerror = function (e) {
            if (typeof failure === 'function') {
                failure(e);
            } else {
                console.log('upload img error: ' + e);
            }
        };
        xhr.send(formData);
    }
};