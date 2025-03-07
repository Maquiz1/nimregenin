// const dbName = "offlineDB";
// const storeName = "dataStore";

// // Function to open IndexedDB
// function openDB() {
//     return new Promise((resolve, reject) => {
//         const request = indexedDB.open(dbName, 1);

//         request.onupgradeneeded = (event) => {
//             const db = event.target.result;
//             if (!db.objectStoreNames.contains(storeName)) {
//                 db.createObjectStore(storeName, { keyPath: "id", autoIncrement: true });
//             }
//         };

//         request.onsuccess = () => resolve(request.result);
//         request.onerror = () => reject("Error opening IndexedDB");
//     });
// }

// // Function to save data offline in IndexedDB
// async function saveOfflineData(data) {
//     const db = await openDB();
//     const transaction = db.transaction(storeName, "readwrite");
//     const store = transaction.objectStore(storeName);
//     store.add(data);
// }

// // Function to sync data when online
// async function syncDataWithServer() {
//     const db = await openDB();
//     const transaction = db.transaction(storeName, "readonly");
//     const store = transaction.objectStore(storeName);
//     const request = store.getAll();

//     request.onsuccess = async () => {
//         const allData = request.result;
//         if (allData.length > 0) {
//             // Send data to server
//             fetch("sync.php", {
//                 method: "POST",
//                 headers: { "Content-Type": "application/json" },
//                 body: JSON.stringify(allData)
//             })
//                 .then(response => response.text())
//                 .then(async () => {
//                     // Clear IndexedDB after successful sync
//                     const transaction = db.transaction(storeName, "readwrite");
//                     transaction.objectStore(storeName).clear();
//                     console.log("Data synced and cleared from IndexedDB");
//                 });
//         }
//     };
// }

// // Listen for form submission
// document.getElementById("userForm").addEventListener("submit", async function (event) {
//     event.preventDefault();

//     const name = document.getElementById("name").value;
//     const email = document.getElementById("email").value;
//     const formData = { name, email };

//     if (navigator.onLine) {
//         // If online, send data directly to server
//         fetch("sync.php", {
//             method: "POST",
//             headers: { "Content-Type": "application/json" },
//             body: JSON.stringify([formData])
//         })
//             .then(response => response.text())
//             .then(() => {
//                 console.log("Data sent directly to server");
//             });
//     } else {
//         // If offline, save data locally
//         saveOfflineData(formData);
//         console.log("Data saved offline");
//     }

//     // Clear form fields
//     document.getElementById("name").value = "";
//     document.getElementById("email").value = "";
// });

// // Check for internet connection and sync data
// window.addEventListener("online", syncDataWithServer);