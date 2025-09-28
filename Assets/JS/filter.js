document.addEventListener("DOMContentLoaded", () => {
    const cityInput = document.getElementById("city");
    const professionInput = document.getElementById("profession");
    const priceInput = document.getElementById("price_range");
    const searchButton = document.querySelector("button");
    const serviceGrid = document.querySelector(".services-grid");

    async function fetchServices() {
        const city = cityInput.value.trim();
        const profession = professionInput.value.trim();
        const price = priceInput.value.trim();

        // Construct query parameters dynamically
        const params = new URLSearchParams();
        if (city) params.append("city", city);
        if (profession) params.append("profession", profession);
        if (price && !isNaN(price)) params.append("price_range", parseInt(price, 10));

        try {
            const response = await fetch(`fetch_services.php?${params.toString()}`);
            const services = await response.json();

            if (services.error) {
                console.error("Error:", services.error);
                serviceGrid.innerHTML = "<p style='color: red;'>Error fetching data</p>";
            } else {
                renderServices(services);
            }
        } catch (error) {
            console.error("Error fetching services:", error);
            serviceGrid.innerHTML = "<p style='color: red;'>Failed to load services</p>";
        }
    }

    function renderServices(serviceList) {
        serviceGrid.innerHTML = "";

        if (serviceList.length === 0) {
            serviceGrid.innerHTML = "<p style='color: red;'>No services available</p>";
            return;
        }

        serviceList.forEach(service => {
            const card = document.createElement("div");
            card.classList.add("card");
            card.innerHTML = `
                <h3>${service.name}</h3>
                <p>${service.profession} - ${service.city}</p>
                <p>₹${service.charge}/day</p>
                <p><strong>Phone:</strong> ${service.phone || "N/A"}</p>
            `;
            serviceGrid.appendChild(card);
        });
    }

    searchButton.addEventListener("click", fetchServices);
});
