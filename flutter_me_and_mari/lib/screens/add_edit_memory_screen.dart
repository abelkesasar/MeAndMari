import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../models/memory.dart';
import '../services/api_service.dart';

class AddEditMemoryScreen extends StatefulWidget {
  final Memory? memory;
  const AddEditMemoryScreen({super.key, this.memory});

  @override
  State<AddEditMemoryScreen> createState() => _AddEditMemoryScreenState();
}

class _AddEditMemoryScreenState extends State<AddEditMemoryScreen> {
  final _formKey = GlobalKey<FormState>();
  final ApiService _apiService = ApiService();
  final ImagePicker _picker = ImagePicker();

  late TextEditingController _titleController;
  late TextEditingController _descController;
  late TextEditingController _locController;
  
  String _happyMeter = 'Sangat Senang';
  final List<String> _happyOptions = [
    'Sangat Sedih',
    'Sedih',
    'Biasa Aja',
    'Senang',
    'Sangat Senang'
  ];
  
  String? _currentPhoto;
  File? _selectedCover;
  List<File> _selectedPhotos = [];
  List<File> _selectedVideos = [];
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _titleController = TextEditingController(text: widget.memory?.title);
    _descController = TextEditingController(text: widget.memory?.description);
    _locController = TextEditingController(text: widget.memory?.location);
    
    if (widget.memory != null && _happyOptions.contains(widget.memory!.happyMeter)) {
      _happyMeter = widget.memory!.happyMeter;
    }
    
    _currentPhoto = widget.memory?.photo;
  }

  Future<void> _pickCover() async {
    final XFile? image = await _picker.pickImage(source: ImageSource.gallery);
    if (image != null && mounted) {
      setState(() => _selectedCover = File(image.path));
    }
  }

  Future<void> _pickMultiplePhotos() async {
    final List<XFile> images = await _picker.pickMultiImage();
    if (images.isNotEmpty && mounted) {
      setState(() => _selectedPhotos.addAll(images.map((e) => File(e.path))));
    }
  }

  Future<void> _pickVideos() async {
    final XFile? video = await _picker.pickVideo(source: ImageSource.gallery);
    if (video != null && mounted) {
      setState(() => _selectedVideos.add(File(video.path)));
    }
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    
    setState(() => _isSaving = true);
    try {
      String photoName = _currentPhoto ?? '';
      
      // Upload cover if selected
      if (_selectedCover != null) {
        final uploadedName = await _apiService.uploadFile(_selectedCover!.path);
        if (uploadedName != null) photoName = uploadedName;
      }

      // Upload extra photos
      List<String> extraPhotos = await _apiService.uploadMultipleFiles(_selectedPhotos.map((e) => e.path).toList());
      
      // Upload videos
      List<String> extraVideos = await _apiService.uploadMultipleFiles(_selectedVideos.map((e) => e.path).toList());

      final data = {
        'id': widget.memory?.id ?? 0,
        'title': _titleController.text,
        'description': _descController.text,
        'location': _locController.text,
        'happy_meter': _happyMeter,
        'photo': photoName,
        'photos': extraPhotos,
        'videos': extraVideos,
      };

      final res = await _apiService.saveMemory(data);
      if (res['status'] == 'success') {
        if (!mounted) return;
        Navigator.pop(context, true);
      } else {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(res['message'])));
      }
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e')));
    } finally {
      if (mounted) {
        setState(() => _isSaving = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.memory == null ? 'Tambah Kenangan' : 'Edit Kenangan'),
      ),
      body: _isSaving 
        ? const Center(child: CircularProgressIndicator())
        : SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Foto Sampul', style: TextStyle(fontWeight: FontWeight.bold)),
                  const SizedBox(height: 8),
                  GestureDetector(
                    onTap: _pickCover,
                    child: Container(
                      height: 180,
                      width: double.infinity,
                      decoration: BoxDecoration(
                        color: Colors.grey.shade100,
                        borderRadius: BorderRadius.circular(20),
                        image: _selectedCover != null 
                          ? DecorationImage(image: FileImage(_selectedCover!), fit: BoxFit.cover)
                          : (_currentPhoto != null 
                              ? DecorationImage(image: NetworkImage('${ApiService.uploadUrl}$_currentPhoto'), fit: BoxFit.cover)
                              : null),
                      ),
                      child: (_selectedCover == null && _currentPhoto == null)
                        ? const Icon(Icons.add_a_photo, size: 50, color: Colors.grey)
                        : const Align(
                            alignment: Alignment.bottomRight,
                            child: Padding(
                              padding: EdgeInsets.all(12),
                              child: CircleAvatar(backgroundColor: Colors.indigo, child: Icon(Icons.edit, color: Colors.white, size: 20)),
                            ),
                          ),
                    ),
                  ),
                  const SizedBox(height: 24),
                  TextFormField(
                    controller: _titleController,
                    decoration: const InputDecoration(labelText: 'Judul Kenangan', border: OutlineInputBorder()),
                    validator: (v) => v!.isEmpty ? 'Judul wajib diisi' : null,
                  ),
                  const SizedBox(height: 16),
                  TextFormField(
                    controller: _locController,
                    decoration: const InputDecoration(labelText: 'Lokasi', border: OutlineInputBorder()),
                  ),
                  const SizedBox(height: 16),
                  DropdownButtonFormField<String>(
                    value: _happyMeter,
                    decoration: const InputDecoration(labelText: 'Happy Meter', border: OutlineInputBorder()),
                    items: _happyOptions.map((String option) {
                      return DropdownMenuItem<String>(
                        value: option,
                        child: Text(option),
                      );
                    }).toList(),
                    onChanged: (String? newValue) {
                      setState(() {
                        _happyMeter = newValue!;
                      });
                    },
                  ),
                  const SizedBox(height: 16),
                  TextFormField(
                    controller: _descController,
                    decoration: const InputDecoration(labelText: 'Cerita/Deskripsi', border: OutlineInputBorder()),
                    maxLines: 4,
                  ),
                  const SizedBox(height: 24),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Foto Tambahan', style: TextStyle(fontWeight: FontWeight.bold)),
                      TextButton.icon(onPressed: _pickMultiplePhotos, icon: const Icon(Icons.add_photo_alternate), label: const Text('Tambah')),
                    ],
                  ),
                  if (_selectedPhotos.isNotEmpty)
                    SizedBox(
                      height: 80,
                      child: ListView.builder(
                        scrollDirection: Axis.horizontal,
                        itemCount: _selectedPhotos.length,
                        itemBuilder: (context, index) => Container(
                          width: 80,
                          margin: const EdgeInsets.only(right: 8),
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(8),
                            image: DecorationImage(image: FileImage(_selectedPhotos[index]), fit: BoxFit.cover),
                          ),
                        ),
                      ),
                    ),
                  const SizedBox(height: 16),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Video', style: TextStyle(fontWeight: FontWeight.bold)),
                      TextButton.icon(onPressed: _pickVideos, icon: const Icon(Icons.video_call), label: const Text('Tambah')),
                    ],
                  ),
                  if (_selectedVideos.isNotEmpty)
                    SizedBox(
                      height: 80,
                      child: ListView.builder(
                        scrollDirection: Axis.horizontal,
                        itemCount: _selectedVideos.length,
                        itemBuilder: (context, index) => Container(
                          width: 80,
                          margin: const EdgeInsets.only(right: 8),
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(8),
                            color: Colors.black,
                          ),
                          child: const Center(child: Icon(Icons.play_circle_fill, color: Colors.white)),
                        ),
                      ),
                    ),
                  const SizedBox(height: 32),
                  ElevatedButton(
                    onPressed: _save,
                    style: ElevatedButton.styleFrom(
                      minimumSize: const Size(double.infinity, 60),
                      backgroundColor: Colors.indigo,
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    ),
                    child: const Text('Simpan Kenangan', style: TextStyle(fontWeight: FontWeight.bold)),
                  ),
                ],
              ),
            ),
          ),
    );
  }
}
