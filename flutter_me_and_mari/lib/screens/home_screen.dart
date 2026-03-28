import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../services/api_service.dart';
import 'main_container.dart';
import 'login_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final ApiService _apiService = ApiService();
  List<String> _photos = [];

  @override
  void initState() {
    super.initState();
    _fetchPhotos();
  }

  void _fetchPhotos() async {
    try {
      final memories = await _apiService.getMemories();
      setState(() {
        _photos = memories.map((m) => m.photo).toList();
      });
    } catch (e) {
      debugPrint('Error fetching photos: $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        children: [
          // Background Scroller
          if (_photos.isNotEmpty)
            Opacity(
              opacity: 0.15,
              child: GridView.builder(
                physics: const NeverScrollableScrollPhysics(),
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 3,
                  crossAxisSpacing: 10,
                  mainAxisSpacing: 10,
                ),
                itemCount: 30, // Duplicate photos to fill screen
                itemBuilder: (context, index) {
                  return ClipRRect(
                    borderRadius: BorderRadius.circular(12),
                    child: CachedNetworkImage(
                      imageUrl: '${ApiService.uploadUrl}${_photos[index % _photos.length]}',
                      fit: BoxFit.cover,
                    ),
                  );
                },
              ),
            ),
          Container(
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
                colors: [
                  Colors.white.withOpacity(0.8),
                  Colors.indigo.shade50.withOpacity(0.9),
                ],
              ),
            ),
          ),
          Center(
            child: Padding(
              padding: const EdgeInsets.all(32.0),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Hero(
                    tag: 'app-logo',
                    child: Container(
                      width: 90,
                      height: 90,
                      decoration: BoxDecoration(
                        color: Colors.indigo,
                        borderRadius: BorderRadius.circular(30),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.indigo.withOpacity(0.3),
                            blurRadius: 25,
                            offset: const Offset(0, 10),
                          ),
                        ],
                      ),
                      child: const Icon(FontAwesomeIcons.solidHeart, color: Colors.white, size: 36),
                    ),
                  ),
                  const SizedBox(height: 32),
                  const Text(
                    'Abel & Mari',
                    style: TextStyle(fontSize: 36, fontWeight: FontWeight.bold, letterSpacing: -1.5, color: Color(0xFF1E293B)),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    '"Every moment with you is a treasure"',
                    style: TextStyle(fontSize: 16, color: Colors.blueGrey.shade500, fontStyle: FontStyle.italic, fontWeight: FontWeight.w500),
                  ),
                  const SizedBox(height: 60),
                  ElevatedButton(
                    onPressed: () {
                      Navigator.push(context, MaterialPageRoute(builder: (context) => const MainContainer()));
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF0F172A),
                      foregroundColor: Colors.white,
                      minimumSize: const Size(double.infinity, 64),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                      elevation: 8,
                      shadowColor: Colors.black.withOpacity(0.3),
                    ),
                    child: const Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text('Lihat Kenangan Kita', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                        SizedBox(width: 12),
                        Icon(Icons.arrow_forward_rounded, size: 20),
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),
                  Row(
                    children: [
                      Expanded(
                        child: _UserButton(
                          label: 'Abel',
                          onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const LoginScreen(user: 'abel'))),
                        ),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: _UserButton(
                          label: 'Mari',
                          onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const LoginScreen(user: 'mari'))),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _UserButton extends StatelessWidget {
  final String label;
  final VoidCallback onPressed;

  const _UserButton({required this.label, required this.onPressed});

  @override
  Widget build(BuildContext context) {
    return OutlinedButton(
      onPressed: onPressed,
      style: OutlinedButton.styleFrom(
        minimumSize: const Size(0, 56),
        side: BorderSide(color: Colors.blueGrey.shade200, width: 1.5),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
        backgroundColor: Colors.white.withOpacity(0.5),
      ),
      child: Text(
        label,
        style: TextStyle(color: Colors.blueGrey.shade700, fontWeight: FontWeight.bold, fontSize: 15),
      ),
    );
  }
}
