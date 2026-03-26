import { setMainMinHeight } from "./helpers/layout.js";

document.addEventListener('DOMContentLoaded', function () {
    setMainMinHeight();
});


// let ready = (callback) => {
//   if (document.readyState != "loading") callback();
//   else document.addEventListener("DOMContentLoaded", callback);
// }

// ready(() => { 
//   /* Làm gì đó khi DOM đã được tải hết */
// });