import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';
import 'widgets/video_player_widget.dart';

class DetailScreen extends StatelessWidget {
  final int memoryId;
  const DetailScreen({super.key, required this.memoryId});

  @override
  Widget build(BuildContext context) {
    final ApiService apiService = ApiService();

    return Scaffold(
      body: FutureBuilder<Map<String, dynamic>>(
        future: apiService.getMemoryDetail(memoryId),
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }
          if (snapshot.hasError) return Center(child: Text('Error: ${snapshot.error}'));
          if (!snapshot.hasData || snapshot.data!['status'] == 'error') {
            return const Center(child: Text('Gagal memuat detail kenangan'));
          }
          
          final data = snapshot.data!['data'];
          if (data == null) return const Center(child: Text('Data tidak ditemukan'));
          
          final memory = data['memory'];
          final photos = (data['photos'] as List?) ?? [];
          final videos = (data['videos'] as List?) ?? [];
          final mainVideo = memory['video'];

          return CustomScrollView(
            slivers: [
              SliverAppBar(
                expandedHeight: 450,
                pinned: true,
                backgroundColor: Colors.indigo,
                flexibleSpace: FlexibleSpaceBar(
                  background: Hero(
                    tag: 'memory-$memoryId',
                    child: CachedNetworkImage(
                      imageUrl: '${ApiService.uploadUrl}${memory['photo']}',
                      fit: BoxFit.cover,
                    ),
                  ),
                ),
              ),
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.all(24.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Wrap(
                        spacing: 8,
                        runSpacing: 8,
                        crossAxisAlignment: WrapCrossAlignment.center,
                        children: [
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                            decoration: BoxDecoration(color: Colors.indigo, borderRadius: BorderRadius.circular(10)),
                            child: Text(memory['happy_meter'], style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold)),
                          ),
                          if (memory['created_at'] != null && memory['created_at'] != "")
                            Text(
                              _formatDate(memory['created_at']),
                              style: TextStyle(color: Colors.blueGrey.shade400, fontSize: 12, fontWeight: FontWeight.w600),
                            ),
                          const Icon(Icons.location_on, size: 14, color: Colors.indigo),
                          Text(
                            memory['location'], 
                            style: const TextStyle(color: Colors.black54, fontWeight: FontWeight.bold, fontSize: 13),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),
                      Text(memory['title'], style: const TextStyle(fontSize: 32, fontWeight: FontWeight.bold)),
                      const SizedBox(height: 24),
                      Text(memory['description'], style: const TextStyle(fontSize: 16, height: 1.6, fontStyle: FontStyle.italic, color: Colors.black87)),
                      const SizedBox(height: 40),
                      
                      const Text('Album', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
                      const SizedBox(height: 16),
                      
                      GridView.builder(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: 2, 
                          crossAxisSpacing: 12, 
                          mainAxisSpacing: 12,
                          childAspectRatio: 1.0,
                        ),
                        itemCount: (mainVideo != null && mainVideo != "" ? 1 : 0) + videos.length + photos.length,
                        itemBuilder: (context, index) {
                          int videoCount = (mainVideo != null && mainVideo != "" ? 1 : 0) + videos.length;
                          
                          if (index < videoCount) {
                            // Render Videos
                            String vUrl = (mainVideo != null && mainVideo != "" && index == 0) 
                              ? mainVideo 
                              : videos[index - (mainVideo != null && mainVideo != "" ? 1 : 0)];
                            return GestureDetector(
                              onTap: () => _showMediaPopup(context, vUrl, true),
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(16),
                                child: Container(
                                  color: Colors.black,
                                  child: MemoryVideoPlayer(videoUrl: '${ApiService.uploadUrl}$vUrl'),
                                ),
                              ),
                            );
                          } else {
                            // Render Photos
                            int photoIndex = index - videoCount;
                            String pUrl = photos[photoIndex];
                            return GestureDetector(
                              onTap: () => _showMediaPopup(context, pUrl, false),
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(16),
                                child: CachedNetworkImage(
                                  imageUrl: '${ApiService.uploadUrl}$pUrl', 
                                  fit: BoxFit.cover
                                ),
                              ),
                            );
                          }
                        },
                      ),
                    ],
                  ),
                ),
              ),
            ],
          );
        },
      ),
    );
  }

  String _formatDate(String dateStr) {
    try {
      return DateFormat('dd MMM yy').format(DateTime.parse(dateStr));
    } catch (e) {
      return dateStr;
    }
  }

  void _showMediaPopup(BuildContext context, String url, bool isVideo) {
    showDialog(
      context: context,
      builder: (context) => Dialog(
        backgroundColor: Colors.transparent,
        insetPadding: const EdgeInsets.all(10),
        child: Stack(
          alignment: Alignment.center,
          children: [
            GestureDetector(
              onTap: () => Navigator.pop(context),
              child: Container(
                width: double.infinity,
                height: double.infinity,
                color: Colors.transparent,
              ),
            ),
            ClipRRect(
              borderRadius: BorderRadius.circular(20),
              child: Container(
                color: Colors.black,
                child: isVideo 
                  ? MemoryVideoPlayer(videoUrl: '${ApiService.uploadUrl}$url')
                  : CachedNetworkImage(
                      imageUrl: '${ApiService.uploadUrl}$url',
                      fit: BoxFit.contain,
                    ),
              ),
            ),
            Positioned(
              top: 10,
              right: 10,
              child: CircleAvatar(
                backgroundColor: Colors.black45,
                child: IconButton(
                  icon: const Icon(Icons.close, color: Colors.white),
                  onPressed: () => Navigator.pop(context),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
