// Performance optimizations - passive event listeners
window.addEventListener(
  "scroll",
  function () {
    const navbar = document.querySelector(".navbar-modern");
    if (window.scrollY > 50) {
      navbar.classList.add("scrolled");
    } else {
      navbar.classList.remove("scrolled");
    }
  },
  { passive: true },
);

// Lazy loading for images
document.addEventListener("DOMContentLoaded", function () {
  const images = document.querySelectorAll("img[data-src]");
  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.classList.add("loaded");
        img.removeAttribute("data-src");
        imageObserver.unobserve(img);
      }
    });
  });

  images.forEach((img) => imageObserver.observe(img));
});

// Debounced scroll handler for performance
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Scroll to products section and open modal
function scrollToProductAndOpenModal(productIndex) {
  // Scroll to products section
  const productsSection = document.getElementById("products");
  if (productsSection) {
    productsSection.scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  }

  // Wait a bit for scroll to complete, then open modal
  setTimeout(() => {
    openModal(productIndex);
  }, 800);
}

// Scroll to products section only
function scrollToProducts() {
  const productsSection = document.getElementById("products");
  if (productsSection) {
    productsSection.scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  }
}

// بيانات كل المنتجات
const allProducts = [
  {
    images: ["images/POLO1.jpg"],
    title: "تيشيرت بولو كحلي",
    price: "60$",
    desc: "خامة قطنية 100% مريحة جداً بتصميم كلاسيكي.",
  },
  {
    images: ["images/POLO2.jpg", "images/POLO3.jpg"],
    title: "بولو جيك آند جونز",
    price: "120$",
    desc: "تصميم عصري من ماركة عالمية بخامات ممتازة.",
  },
  {
    images: ["images/POLO3.jpg"],
    title: "بولو كم طويل",
    price: "100$",
    desc: "مثالي للأجواء الخريفية مع خامة تريكو ناعمة.",
  },
  {
    images: ["images/POLO4.jpg"],
    title: "بولو هاواي - أوراق شجر",
    price: "110$",
    desc: "نقشة أوراق شجر صيفية جذابة.",
  },
  {
    images: ["images/كروب توب أديداس.jpg"],
    title: "كروب توب أديداس",
    price: "150$",
    desc: "ملابس رياضية أصلية مريحة للجيم.",
  },
  {
    images: ["images/تيشيرت Delightful.jpg"],
    title: "تيشيرت Delightful",
    price: "60$",
    desc: "تصميم نمر عصري لإطلالة كاجوال.",
  },
  {
    images: ["images/تيشيرت كاجوال مخطط.jpg"],
    title: "تيشيرت كاجوال مخطط",
    price: "90$",
    desc: "تيشيرت كلاسيكي مخطط أبيض في أسود.",
  },
  {
    images: ["images/حذاء رياضي.jpg"],
    title: "حذاء رياضي عصري",
    price: "70$",
    desc: "نعل مريح وتصميم خفيف للمشي والجري.",
  },
];

// بيانات السلة الحالية - فاضية من الأول
let cartItems = [];

let currentProductIndex = 0;

// Bootstrap Modal event listeners
document.addEventListener("DOMContentLoaded", function () {
  const modalElement = document.getElementById("productModal");
  if (modalElement) {
    modalElement.addEventListener("shown.bs.modal", function () {
      console.log("Modal is now shown");
      updateModalContent(currentProductIndex);
    });

    modalElement.addEventListener("hidden.bs.modal", function () {
      console.log("Modal is now hidden");
      // Remove backdrop manually if stuck
      const backdrop = document.querySelector(".modal-backdrop");
      if (backdrop) {
        backdrop.remove();
      }
      // Remove modal-open class from body
      document.body.classList.remove("modal-open");
      document.body.style.overflow = "";
      document.body.style.paddingRight = "";
    });

    // Add backdrop click handler to close modal
    modalElement.addEventListener("click", function (e) {
      if (e.target === modalElement) {
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
          modal.hide();
        }
      }
    });
  }
});

/**
 * وظيفة فتح المودال وتحديث البيانات ديناميكاً
 * @param {Number} productIndex - رقم المنتج في المصفوفة
 */
function openModal(productIndex) {
  currentProductIndex = productIndex;
  console.log("Opening modal with product index:", productIndex);

  // Open modal first
  const modal = new bootstrap.Modal(document.getElementById("productModal"));
  modal.show();
}

/**
 * تحديث محتوى المودال بالمنتج الحالي
 */
function updateModalContent(productIndex) {
  const product = allProducts[productIndex];

  // 1. تحديث النصوص الأساسية في المودال
  document.getElementById("modalTitle").innerText = product.title;
  document.getElementById("modalPrice").innerText = product.price;
  document.getElementById("modalDesc").innerText = product.desc;

  // 2. بناء الـ HTML للصور الجديدة
  const carouselInner = document.getElementById("modalCarouselInner");
  carouselInner.innerHTML = "";

  product.images.forEach((imgSrc, index) => {
    const isActive = index === 0 ? "active" : "";
    carouselInner.innerHTML += `
            <div class="carousel-item ${isActive}">
                <img src="${imgSrc}" class="d-block w-100 rounded-3" 
                     style="height: 350px; object-fit: contain; background-color: #f8f9fa;">
            </div>
        `;
  });

  // 3. تحديث الـ Carousel للصور
  const myCarouselElement = document.querySelector("#carouselExampleControls");
  let carouselInstance = bootstrap.Carousel.getInstance(myCarouselElement);
  if (carouselInstance) {
    carouselInstance.dispose();
  }

  carouselInstance = new bootstrap.Carousel(myCarouselElement, {
    ride: false,
  });
  carouselInstance.to(0);

  // 4. تحديث أسهم الصور
  const prevBtn = document.querySelector(".carousel-control-prev");
  const nextBtn = document.querySelector(".carousel-control-next");
  if (product.images.length <= 1) {
    prevBtn.style.display = "none";
    nextBtn.style.display = "none";
  } else {
    prevBtn.style.display = "flex";
    nextBtn.style.display = "flex";
  }

  // 5. تحديث عداد المنتجات
  setTimeout(() => {
    const counterElement = document.getElementById("productCounter");
    if (counterElement) {
      counterElement.innerText = `${productIndex + 1} / ${allProducts.length}`;
      counterElement.style.color = "red !important"; // temporary for debugging
      console.log(
        "Counter updated to:",
        `${productIndex + 1} / ${allProducts.length}`,
      );
    } else {
      console.log("Counter element not found!");
    }
  }, 100);
}

/**
 * تحديث العداد وإظهار المنتج التالي
 */
function updateCounterAndShowNext() {
  currentProductIndex = (currentProductIndex + 1) % allProducts.length;
  console.log("Next product index:", currentProductIndex);

  // Update counter immediately
  const counterElement = document.getElementById("productCounter");
  if (counterElement) {
    counterElement.innerText = `${currentProductIndex + 1} / ${allProducts.length}`;
    counterElement.style.color = "red !important";
    console.log(
      "Counter updated to:",
      `${currentProductIndex + 1} / ${allProducts.length}`,
    );
  }

  // Update modal content
  updateModalContent(currentProductIndex);
}

/**
 * تحديث العداد وإظهار المنتج السابق
 */
function updateCounterAndShowPrev() {
  currentProductIndex =
    (currentProductIndex - 1 + allProducts.length) % allProducts.length;
  console.log("Prev product index:", currentProductIndex);

  // Update counter immediately
  const counterElement = document.getElementById("productCounter");
  if (counterElement) {
    counterElement.innerText = `${currentProductIndex + 1} / ${allProducts.length}`;
    counterElement.style.color = "red !important";
    console.log(
      "Counter updated to:",
      `${currentProductIndex + 1} / ${allProducts.length}`,
    );
  }

  // Update modal content
  updateModalContent(currentProductIndex);
}

/**
 * حذف المنتج من السلة
 */
function removeFromCart(button) {
  const productBox = button.closest(".box");

  // إضافة تأثير الحذف
  productBox.style.transition = "all 0.3s ease";
  productBox.style.opacity = "0";
  productBox.style.transform = "translateX(-100%)";

  // حذف المنتج بعد الأنيميشن
  setTimeout(() => {
    productBox.remove();
    updateCartSummary();
  }, 300);
}

/**
 * تحديث ملخص السلة (العدد والإجمالي)
 */
function updateCartSummary() {
  const cartCount = cartItems.length;

  // تحديث عداد المنتجات في الهيدر
  const cartTitle = document.getElementById("offcanvasExampleLabel");
  cartTitle.innerText = `سلة المشتريات (${cartCount})`;

  // تحديث عداد المنتجات في زرار السلة
  const cartCountElement = document.querySelector(".cart-count");
  if (cartCountElement) {
    cartCountElement.innerText = cartCount;
  }

  // حساب الإجمالي الجديد من cartItems array
  let total = 0;
  cartItems.forEach((item) => {
    const priceText = item.price;
    const price = parseInt(priceText.replace("$", ""));
    total += price;
  });

  // تحديث الإجمالي
  const totalElement = document.getElementById("cartTotal");
  if (totalElement) {
    totalElement.innerText = `${total}$`;
  }

  // لو مفيش منتجات، إظهار رسالة
  if (cartCount === 0) {
    const offcanvasBody = document.querySelector(".offcanvas-body");
    offcanvasBody.innerHTML = `
            <div class="text-center py-5">
                <i class="fa-solid fa-shopping-cart fa-3x empty-cart-icon text-muted mb-3"></i>
                <h5 class="text-muted">السلة فارغة</h5>
                <p class="text-muted">أضف بعض المنتجات للبدء</p>
            </div>
            <div class="cart-footer">
                <h4 class="d-flex justify-content-between align-items-center">
                    <span>الإجمالي:</span>
                    <span class="text-primary">0$</span>
                </h4>
                <button class="btn btn-dark w-100" disabled>
                    إكمال عملية الشراء <i class="fa-solid fa-arrow-left ms-2"></i>
                </button>
            </div>
        `;
  }
}

/**
 * إضافة المنتج للسلة
 */
function addToCart(productId) {
  const product = allProducts[productId];

  // check if product already in cart
  const existingItem = cartItems.find((item) => item.id === productId);
  if (existingItem) {
    // show notification that product already in cart
    showNotification("المنتج موجود بالفعل في السلة!");
    return;
  }

  // add product to cart
  cartItems.push({
    id: productId,
    title: product.title,
    price: product.price,
    image: product.images[0],
  });

  // update UI
  renderCart();
  showNotification("تمت إضافة المنتج للسلة بنجاح!");
}

/**
 * عرض الإشعارات
 */
function showNotification(message) {
  // create notification element
  const notification = document.createElement("div");
  notification.className =
    "cart-notification position-fixed top-0 start-50 translate-middle-x mt-4";
  notification.style.zIndex = "9999";
  notification.style.minWidth = "350px";
  notification.innerHTML = `
    <div class="notification-content">
      <div class="notification-icon">
        <i class="fa-solid fa-check-circle"></i>
      </div>
      <div class="notification-text">
        <div class="notification-title">تمت الإضافة بنجاح!</div>
        <div class="notification-message">${message}</div>
      </div>
      <div class="notification-close">
        <i class="fa-solid fa-times"></i>
      </div>
    </div>
  `;

  document.body.appendChild(notification);

  // Add close functionality
  const closeBtn = notification.querySelector(".notification-close");
  closeBtn.addEventListener("click", () => {
    notification.classList.add("removing");
    setTimeout(() => {
      notification.remove();
    }, 500);
  });

  // Auto remove after 5 seconds
  setTimeout(() => {
    if (notification.parentNode) {
      notification.classList.add("removing");
      setTimeout(() => {
        notification.remove();
      }, 500);
    }
  }, 5000);
}

/**
 * عرض محتويات السلة
 */
function renderCart() {
  const offcanvasBody = document.querySelector(".offcanvas-body");

  if (cartItems.length === 0) {
    offcanvasBody.innerHTML = `
            <div class="text-center py-5">
                <i class="fa-solid fa-shopping-cart fa-3x empty-cart-icon text-muted mb-3"></i>
                <h5 class="text-muted">السلة فارغة</h5>
                <p class="text-muted">أضف بعض المنتجات للبدء</p>
            </div>
            <div class="cart-footer">
                <h4 class="d-flex justify-content-between align-items-center">
                    <span>الإجمالي:</span>
                    <span class="text-primary">0$</span>
                </h4>
                <button class="btn btn-dark w-100" disabled>
                    إكمال عملية الشراء <i class="fa-solid fa-arrow-left ms-2"></i>
                </button>
            </div>
        `;
    updateCartSummary();
    return;
  }

  let cartHTML = "";
  cartItems.forEach((item, index) => {
    cartHTML += `
            <div class="cart-item">
                <div class="img-wrapper">
                    <img class="offcan-img" src="${item.image}" alt="${item.title}">
                </div>
                <button class="btn btn-danger btn-sm" onclick="removeFromCartByIndex(${index})">
                    <i class="fa-solid fa-trash"></i>
                </button>
                <p class="product-name">${item.title}</p>
                <span class="price">${item.price}</span>
            </div>
            ${index < cartItems.length - 1 ? '<hr class="my-3" style="border: none; height: 1px; background: #e9ecef;">' : ""}
        `;
  });

  cartHTML += `
        <div class="cart-footer">
            <h4 class="d-flex justify-content-between align-items-center">
                <span>الإجمالي:</span>
                <span class="text-primary" id="cartTotal">0$</span>
            </h4>
            <button class="btn btn-dark w-100">
                إكمال عملية الشراء <i class="fa-solid fa-arrow-left ms-2"></i>
            </button>
        </div>
    `;

  offcanvasBody.innerHTML = cartHTML;
  updateCartSummary();
}

/**
 * حذف المنتج من السلة بال اندكس
 */
function removeFromCartByIndex(index) {
  const productBox = document.querySelectorAll(".offcanvas-body .cart-item")[
    index
  ];

  // إضافة تأثير الحذف
  productBox.style.transition = "all 0.3s ease";
  productBox.style.opacity = "0";
  productBox.style.transform = "translateX(-100%)";

  // حذف المنتج بعد الأنيميشن
  setTimeout(() => {
    cartItems.splice(index, 1);
    renderCart();
  }, 300);
}

// تفعيل ميزة الفلاتر (شكل جمالي)
document.querySelectorAll(".product-filters .btn").forEach((btn) => {
  btn.addEventListener("click", function () {
    const filterType = this.innerText.trim();
    const productCards = document.querySelectorAll("#products .col-md-6");

    // إزالة الكلاس active من كل الأزرار
    document.querySelectorAll(".product-filters .btn").forEach((b) => {
      b.classList.remove(
        "active",
        "btn-primary",
        "btn-danger",
        "btn-success",
        "btn-warning",
      );
    });

    // إضافة الكلاس active للزرار اللي ضغطت عليه
    this.classList.add("active");

    // تحديد شكل الزرار حسب النوع - كل الأزرار outline
    if (filterType === "الخصومات %") {
      this.classList.add("btn-danger");
    } else if (filterType === "الأحدث") {
      this.classList.add("btn-success");
    } else if (filterType === "الأكثر مبيعاً") {
      this.classList.add("btn-warning");
    } else {
      // أي فلتر تاني يبقى outline
      this.classList.add("btn-outline-primary");
    }

    // تطبيق الفلتر على المنتجات
    productCards.forEach((card) => {
      const badge = card.querySelector(".badge");

      if (filterType === "الكل") {
        card.style.display = "block";
      } else if (filterType === "الخصومات %") {
        if (badge && badge.innerText.includes("خصم")) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      } else if (filterType === "الأحدث") {
        if (badge && badge.innerText.includes("أحدث")) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      } else if (filterType === "الأكثر مبيعاً") {
        if (
          badge &&
          (badge.innerText.includes("خصم 40%") ||
            badge.innerText.includes("خصم 30%"))
        ) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      } else {
        card.style.display = "block";
      }
    });
  });
});

// End of JavaScript code
