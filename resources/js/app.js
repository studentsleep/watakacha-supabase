import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
window.Alpine = Alpine;
window.Swal = Swal;
Alpine.start();
// (เราลบ Alpine.js ออกจากตรงนี้)
/**
 * ฟังก์ชันใหม่สำหรับเปิด/ปิด Modal (แบบ Tailwind)
 * @param {string} modalId - ID ของ Modal ที่จะเปิด/ปิด
 * @param {boolean} show - true เพื่อแสดง, false เพื่อซ่อน
 * @param {Event} [event] - (Optional) Event ที่เกิดขึ้น
 */
window.toggleModal = function(modalId, show, event) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    // (ป้องกันการปิด Modal เมื่อคลิกที่เนื้อหาข้างใน)
    if (event && show === false && event.target !== modal) {
        return;
    }
    
    if (show) {
        modal.classList.remove('hidden');
    } else {
        modal.classList.add('hidden');
    }
}