

jQuery(document).ready(function ($) {
    let order_total = $('input[name="themedoni_bnpl_order_total"]').val();
    $("#bnpl-container #rules ul").addClass("list-disc text-slate-500");

    $('input[name="themedoni_bnpl_order_condition_name"]').on(
        "click",
        function (event) {
            event.stopPropagation();

            let installment_name = $(
                'input[name="themedoni_bnpl_order_condition_name"]:checked'
            ).val();

            $(document)
                .ajaxStart(function () {
                    $("#loading-container").addClass("opacity-20");
                    $("#loader").removeClass("hidden");
                })
                .ajaxStop(function () {
                    $("#loading-container").removeClass("opacity-20");
                    $("#loader").addClass("hidden");
                });

            handleAjax(installment_name, order_total);
        }
    );

    function handleAjax(installment_name, order_total) {
        $.ajax({
            type: "post",
            url: ajax_obj.ajax_url,
            data: {
                action: "bnpl_get_data",
                name: installment_name,
                orderTotal: order_total,
                nonce: ajax_obj.nonce,
            },
            success: function (response) {
                let today = new Date();
                response = JSON.parse(response);
                let daysToAdd =
                    parseInt(response.response.term_of_installments) /
                    parseInt(response.response.installments);

                $("#bnpl_prepayment").empty();
                $("#bnpl_installments").empty();
                $("#bnpl_commission_rate").empty();
                $("#bnpl_final_price").empty();
                $("#bnpl_cheque_dates").empty();
                let counter = 1;
                for (let i = 0; i < response.response.installments; i++) {
                    $("#bnpl_prepayment").html(
                        insertrialcamma(
                            toFarsiNumber(JSON.parse(response.response.prepayment))
                        ) + " %"
                    );
                    $("#bnpl_installments").html(
                        toFarsiNumber(JSON.parse(response.response.installments)) + " مورد "
                    );
                    $("#bnpl_term_of_installments").html(
                        toFarsiNumber(JSON.parse(response.response.term_of_installments)) +
                        " روز "
                    );
                    $("#bnpl_commission_rate").html(
                        toFarsiNumber(JSON.parse(response.response.commission_rate)) + " % "
                    );

                    gateway_calculator(response);
                    $("#bnpl_final_price").html(
                        insertrialcamma(toFarsiNumber(gateway_calculator(response))) +
                        " تومان "
                    );

                    let result = today.setDate(today.getDate() + daysToAdd);
                    let mydate = new Date(result);
                    let mypersiandate = mydate.toLocaleDateString("fa-IR");
                    $(
                        '<p class="text-base font-semibold leading-7 text-indigo-600"> تاریخ چک ' +
                        toFarsiNumber(counter) +
                        ': </p><p class="font-bold">' +
                        mypersiandate +
                        "</p>"
                    ).appendTo("#bnpl_cheque_dates");
                    counter++;
                }



                const fileInput = document.getElementById("file-input");
                const dropZone = document.getElementById("drop-zone");
                const selectedImages = document.getElementById("selected-images");
                const selectButton = document.getElementById("select-button");
                const selectedFilesCount = document.getElementById(
                    "selected-files-count"
                );

                // selectButton.addEventListener("click", (event) => {
                //     event.stopPropagation();
                //     fileInput.click();
                // });

                fileInput.addEventListener("change", handleFiles);
                dropZone.addEventListener("dragover", handleDragOver);
                dropZone.addEventListener("dragleave", handleDragLeave);
                dropZone.addEventListener("drop", handleDrop);

                function handleFiles() {
                    const fileList = this.files;
                    displayImages(fileList);
                }
                function handleDragOver(event) {
                    event.preventDefault();
                    dropZone.classList.add("border-blue-500");
                    dropZone.classList.add("text-blue-500");
                }

                function handleDragLeave(event) {
                    event.preventDefault();
                    dropZone.classList.remove("border-blue-500");
                    dropZone.classList.remove("text-blue-500");
                }

                function handleDrop(event) {
                    event.preventDefault();
                    const fileList = event.dataTransfer.files;
                    displayImages(fileList);
                    dropZone.classList.remove("border-blue-500");
                    dropZone.classList.remove("text-blue-500");
                }

                function displayImages(fileList) {
                    if (!(fileList.length > JSON.parse(response.response.installments))) {

                        selectedImages.innerHTML = "";
                        for (const file of fileList) {
                            const imageWrapper = document.createElement("div");
                            imageWrapper.classList.add("relative", "mx-2", "mb-2");
                            const image = document.createElement("img");
                            image.src = URL.createObjectURL(file);
                            image.classList.add("w-32", "h-32", "object-cover", "rounded-lg");
                            const removeButton = document.createElement("button");
                            removeButton.innerHTML = "&times;";
                            removeButton.classList.add(
                                "absolute",
                                "top-1",
                                "right-1",
                                "h-6",
                                "w-6",
                                "bg-gray-700",
                                "text-white",
                                "text-xs",
                                "rounded-full",
                                "flex",
                                "items-center",
                                "justify-center",
                                "opacity-50",
                                "hover:opacity-100",
                                "transition-opacity",
                                "focus:outline-none"
                            );

                            removeButton.addEventListener("click", (event) => {
                                event.stopPropagation();
                                imageWrapper.remove();
                                updateSelectedFilesCount();
                            });

                            imageWrapper.appendChild(image);
                            imageWrapper.appendChild(removeButton);
                            selectedImages.appendChild(imageWrapper);
                        }
                        updateSelectedFilesCount();
                    }
                    updateSelectedFilesCount(JSON.parse(response.response.installments));
                }

                function updateSelectedFilesCount(limited = false) {

                    const count = selectedImages.children.length;
                    if (count > 0) {
                        selectedFilesCount.textContent = `${count} مورد انتخاب شده`;
                    } else if (limited) {
                        selectedFilesCount.textContent = `نمیتوان بیشتر از ${limited} مورد انتخاب کرد`;
                    }
                    else {
                        selectedFilesCount.textContent = "";
                    }
                }
            },
            error: function (error) {
                console.log(error);
            },
        });
    }

    function gateway_calculator(response) {
        let prepayment = response.response.prepayment;
        let installments = response.response.installments;
        let commission_rate = response.response.commission_rate;
        let order_total = response.order_total;

        let prepayment_price = order_total * (prepayment / 100);
        let remained = order_total - prepayment_price;
        let commission_price = remained * (commission_rate / 100);
        let remained_with_commission_price = remained + commission_price;
        let every_installment_price = remained_with_commission_price / installments;

        return Math.floor(remained_with_commission_price + prepayment_price);
    }

    function insertrialcamma(n) {
        var m = "";
        for (var i = 0; i < n.length; i++) {
            var c = n.substr(n.length - i - 1, 1);
            if ((i % 3 == 0) & (i > 0)) {
                m = c + "," + m;
            } else {
                m = c + m;
            }
        }
        return m;
    }

    function toFarsiNumber(n) {
        var o = "";
        n = n.toString();
        const farsiDigits = ["۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹"];
        const englishDigits = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
        for (var i = 0; i < n.length; i++) {
            for (var j = 0; j < englishDigits.length; j++) {
                if (n.substr(i, 1) == englishDigits[j]) {
                    o = o + farsiDigits[j];
                }
            }
        }
        return o;
    }
});
