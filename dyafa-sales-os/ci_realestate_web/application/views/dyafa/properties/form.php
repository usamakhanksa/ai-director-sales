<h2><?php echo $property ? 'Edit Property' : 'Add Property'; ?></h2>
<?php if (!empty($error)): ?><div class="dso-alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php echo validation_errors('<div class="dso-alert error">', '</div>'); ?>
<form class="dso-form" method="post" enctype="multipart/form-data">
    <label>Name</label>
    <input type="text" name="name" value="<?php echo $property ? htmlspecialchars($property->name) : ''; ?>" required>

    <label>City</label>
    <input type="text" name="city" value="<?php echo $property ? htmlspecialchars($property->city) : ''; ?>">

    <label>Address</label>
    <textarea name="address" rows="2"><?php echo $property ? htmlspecialchars($property->address) : ''; ?></textarea>

    <label>Description</label>
    <textarea name="description" rows="3"><?php echo $property ? htmlspecialchars($property->description) : ''; ?></textarea>

    <label>Total Rooms</label>
    <input type="number" name="total_rooms" value="<?php echo $property ? $property->total_rooms : ''; ?>">

    <label>Latitude / Longitude (optional - leave blank to auto-geocode from City)</label>
    <input type="text" name="lat" placeholder="Latitude" value="<?php echo ($property && $property->lat) ? $property->lat : ''; ?>" style="width:48%; display:inline-block;">
    <input type="text" name="lng" placeholder="Longitude" value="<?php echo ($property && $property->lng) ? $property->lng : ''; ?>" style="width:48%; display:inline-block;">

    <?php if ($property && $property->lat && $property->lng): ?>
    <div id="dso-property-map" style="height:250px; margin:10px 0; border-radius:6px;"></div>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var m = L.map('dso-property-map').setView([<?php echo $property->lat; ?>, <?php echo $property->lng; ?>], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(m);
        L.marker([<?php echo $property->lat; ?>, <?php echo $property->lng; ?>]).addTo(m);
    </script>
    <?php endif; ?>

    <label>Map / Photo (gif, jpg, png, pdf - max 5MB)</label>
    <input type="file" name="map_file">
    <?php if ($property && $property->map_file): ?><p><a href="<?php echo base_url('uploads/property_maps/' . $property->map_file); ?>" target="_blank">Current file</a></p><?php endif; ?>

    <label>Property Info Document (pdf, doc, docx - max 5MB)</label>
    <input type="file" name="info_file">
    <?php if ($property && $property->info_file): ?><p><a href="<?php echo base_url('uploads/property_maps/' . $property->info_file); ?>" target="_blank">Current file</a></p><?php endif; ?>

    <label>Status</label>
    <select name="status">
        <?php foreach (array('Active','Inactive') as $s): ?>
        <option value="<?php echo $s; ?>" <?php echo ($property && $property->status == $s) ? 'selected' : ''; ?>><?php echo $s; ?></option>
        <?php endforeach; ?>
    </select>

    <br><button type="submit" class="dso-btn">Save</button>
</form>
