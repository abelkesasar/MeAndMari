import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../services/api_service.dart';
import '../models/memory.dart';

class MeetUsScreen extends StatefulWidget {
  const MeetUsScreen({super.key});

  @override
  State<MeetUsScreen> createState() => _MeetUsScreenState();
}

class _MeetUsScreenState extends State<MeetUsScreen> {
  final ApiService apiService = ApiService();
  
  String abelBio = "Halo! Aku Abel. aku baru mulai belajar belajar prompting nih dan ini salah satu projek mandiri pertama saya. Nice to meet you guys!";
  String mariBio = "Hai! Aku Mari. Mari kita buat lebih banyak kenangan manis.";
  
  Map<String, dynamic>? abelProfile;
  Map<String, dynamic>? mariProfile;
  List<Map<String, dynamic>> allMedia = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    try {
      final data = await apiService.getMeetUsData();
      if (!mounted) return;
      setState(() {
        abelProfile = data['profiles']['abel'];
        mariProfile = data['profiles']['mari'];
        allMedia = List<Map<String, dynamic>>.from(data['all_media']);
        isLoading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() => isLoading = false);
      debugPrint("Error loading meet us data: $e");
    }
  }

  void _showLargeMedia(Map<String, dynamic> item) {
    showDialog(
      context: context,
      builder: (context) => Dialog(
        backgroundColor: Colors.transparent,
        insetPadding: const EdgeInsets.all(10),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Stack(
              alignment: Alignment.topRight,
              children: [
                ClipRRect(
                  borderRadius: BorderRadius.circular(12),
                  child: item['type'] == 'video'
                      ? AspectRatio(
                          aspectRatio: 16 / 9,
                          child: Container(
                            color: Colors.black,
                            child: const Center(
                              child: Icon(Icons.play_circle_fill, color: Colors.white, size: 64),
                            ),
                          ),
                        )
                      : CachedNetworkImage(
                          imageUrl: '${ApiService.uploadUrl}${item['url']}',
                          placeholder: (context, url) => const CircularProgressIndicator(),
                        ),
                ),
                IconButton(
                  icon: const Icon(Icons.close, color: Colors.white),
                  onPressed: () => Navigator.pop(context),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9),
      appBar: AppBar(
        title: const Text('Meet Us', style: TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF0F172A))),
        backgroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: isLoading 
        ? const Center(child: CircularProgressIndicator())
        : SingleChildScrollView(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _buildProfileSection(
                  name: "Abel", 
                  bio: abelBio, 
                  profilePic: abelProfile?['profile_pic'], 
                  themeColor: Colors.indigo,
                  details: {
                    'Birthday': '04 Jun 2005',
                    'Hobbies': 'Coding & Gaming',
                    'Fav Food': 'Nasi Goreng',
                  }
                ),
                const SizedBox(height: 24),
                _buildProfileSection(
                  name: "Mari", 
                  bio: mariBio, 
                  profilePic: mariProfile?['profile_pic'], 
                  themeColor: Colors.pink,
                  details: {
                    'Birthday': '27 May 2004',
                    'Hobbies': 'Music & Reading',
                    'Fav Drink': 'Matcha Latte',
                  }
                ),
                const SizedBox(height: 32),
              ],
            ),
          ),
    );
  }

  Widget _buildProfileSection({
    required String name, 
    required String bio, 
    required String? profilePic, 
    required Color themeColor,
    required Map<String, String> details,
  }) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(32),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  border: Border.all(color: themeColor.withOpacity(0.1), width: 4),
                ),
                child: CircleAvatar(
                  radius: 40,
                  backgroundColor: themeColor.withOpacity(0.1),
                  backgroundImage: profilePic != null 
                    ? CachedNetworkImageProvider('${ApiService.uploadUrl}$profilePic')
                    : null,
                  child: profilePic == null 
                    ? Text(name[0], style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: themeColor))
                    : null,
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(name, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: Color(0xFF0F172A))),
                    Text(
                      name == "Abel" ? "The Boy" : "The Girl", 
                      style: TextStyle(color: themeColor, fontSize: 12, fontWeight: FontWeight.bold, letterSpacing: 1.2)
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: themeColor.withOpacity(0.03),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Column(
              children: details.entries.map((e) => Padding(
                padding: const EdgeInsets.symmetric(vertical: 4),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(e.key, style: TextStyle(color: Colors.blueGrey.shade300, fontSize: 12, fontWeight: FontWeight.bold)),
                    Text(e.value, style: const TextStyle(color: Color(0xFF1E293B), fontSize: 12, fontWeight: FontWeight.bold)),
                  ],
                ),
              )).toList(),
            ),
          ),
          const SizedBox(height: 20),
          const Text("MESSAGE", style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Color(0xFF94A3B8), letterSpacing: 1.5)),
          const SizedBox(height: 8),
          Text(
            bio, 
            style: const TextStyle(color: Color(0xFF475569), fontSize: 14, height: 1.5, fontStyle: FontStyle.italic)
          ),
        ],
      ),
    );
  }

  // Remove _buildMediaGallery as it's no longer needed
}
